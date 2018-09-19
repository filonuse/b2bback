<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreReminder;
use App\Http\Resources\ReminderResource;
use App\Repositories\ReminderRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class ReminderController extends ApiController
{
    /**
     * ReminderController constructor.
     * @param ReminderRepository $repository
     */
    public function __construct(ReminderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/reminders",
     *     tags={"Client: reminders"},
     *     summary="Display a listing of the reminders.",
     *     operationId="clientRemindersIndex",
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ReminderResource"))),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index()
    {
        $reminders = $this->repository->all();

        return ReminderResource::collection($reminders);
    }

    /**
     * @SWG\Post(
     *     path="/client/reminders",
     *     tags={"Client: reminders"},
     *     summary="Store a newly created reminder in storage.",
     *     operationId="clientRemindersStore",
     *     @SWG\Parameter(name="body", in="body", required=true,
     *      @SWG\Schema(type="object",
     *          @SWG\Property(property="description", type="string"),
     *          @SWG\Property(property="date_at", type="string", description="Y-m-d"),
     *          @SWG\Property(property="on_days", type="array", @SWG\Items(type="integer", description="Number of the day (0-6)")),
     *          @SWG\Property(property="time_at", type="string", description="H:i:s UTC"),
     *          @SWG\Property(property="activated", type="boolean"))),
     *     @SWG\Response(response="201", description="Success", @SWG\Schema(ref="#/definitions/ReminderResource")),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function store(StoreReminder $request)
    {
        $data              = $this->getPrepareData($request);
        $data['user_id']   = \Auth::id();

        $reminder = $this->repository->create($data);

        return ReminderResource::make($reminder);
    }

    /**
     * @SWG\Get(
     *     path="/client/reminders/{id}",
     *     tags={"Client: reminders"},
     *     summary="Display the specified reminder.",
     *     operationId="clientRemindersShow",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(ref="#/definitions/ReminderResource")),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function show($id)
    {
        $reminder = $this->repository->findOrFail($id);

        $this->authorize('view', $reminder);

        return ReminderResource::make($reminder);
    }

    /**
     * @SWG\Put(
     *     path="/client/reminders/{id}",
     *     tags={"Client: reminders"},
     *     summary="Update the specified reminder in storage.",
     *     operationId="clientReminderUpdate",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *      @SWG\Schema(type="object",
     *          @SWG\Property(property="description", type="string"),
     *          @SWG\Property(property="date_at", type="string", description="Y-m-d"),
     *          @SWG\Property(property="on_days", type="array", @SWG\Items(type="integer", description="Number of the day (0-6)")),
     *          @SWG\Property(property="time_at", type="string", description="H:i:s UTC"),
     *          @SWG\Property(property="activated", type="boolean"))),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(ref="#/definitions/ReminderResource")),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function update(StoreReminder $request, $id)
    {
        $reminder = $this->repository->findOrFail($id);

        $this->authorize('update', $reminder);

        $reminder->update($this->getPrepareData($request));

        return ReminderResource::make($reminder);
    }

    /**
     * @SWG\Delete(
     *     path="/client/reminders/{id}",
     *     tags={"Client: reminders"},
     *     summary="Remove the specified reminder from storage.",
     *     operationId="clientRemindersDestroy",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $reminder = $this->repository->findOrFail($id);

        $this->authorize('delete', $reminder);

        $reminder->delete();

        return $this->sendSuccessResponse('Reminder deleted successfully.');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getPrepareData(Request $request)
    {
        $data = $request->only('description', 'time_at', 'activated');

        if ($request->has('date_at')) {
            $data['on_days'] = null;
            $data['date_at'] = $request->get('date_at');
        } elseif ($request->has('on_days')) {
            $data['on_days'] = json_encode($request->get('on_days'));
            $data['date_at'] = null;
        }

        $data['activated'] = filter_var($request->get('activated'), FILTER_VALIDATE_BOOLEAN);

        return $data;
    }
}
