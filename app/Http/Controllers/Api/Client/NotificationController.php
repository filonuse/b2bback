<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotificationResource;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;


class NotificationController extends ApiController
{
    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/notifications",
     *     tags={"Client: notifications"},
     *     summary="Display a listing of the notifications.",
     *     operationId="getClientNotification",
     *     @SWG\Parameter(name="page", in="query", type="integer", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="integer", default="5"),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/NotificationResource")) ),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $user          = \Auth::user();
        $notifications = $user->notifications()
            ->paginate((int)$request->get('per_page', 5));

        $total_unread = $user->unreadNotifications->count();

        return NotificationResource::collection($notifications)
            ->additional(['meta' => ['total_unread' => $total_unread]]);
    }

    /**
     * @SWG\Put(
     *     path="/client/notifications/{id}",
     *     tags={"Client: notifications"},
     *     summary="To mark a notification as 'read'.",
     *     operationId="markAsReadClientNotification",
     *     @SWG\Parameter(name="id", in="path", type="string", required=true),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/NotificationResource")) ),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function markAsRead(Request $request, $id)
    {
        $this->repository->update(['read_at' => now()], $id);

        return $this->sendSuccessResponse();
    }
}
