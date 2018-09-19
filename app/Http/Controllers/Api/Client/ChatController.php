<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreMessage;
use App\Http\Resources\ContactResource;
use App\Http\Resources\MessageResource;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class ChatController extends ApiController
{
    /**
     * ChatController constructor.
     * @param MessageRepository $repository
     */
    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/chat/contacts",
     *     summary="Display a listing of the contacts.",
     *     tags={"Client: chat"},
     *     operationId="chatContacts",
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function contacts()
    {
        $users = app('App\Repositories\UserRepository')->contacts(\Auth::user())->get();

        return ContactResource::collection($users);
    }

    /**
     * @SWG\Get(
     *     path="/client/chat/messages/{user}",
     *     summary="Display a listing of the messages.",
     *     tags={"Client: chat"},
     *     operationId="chatMessages",
     *     @SWG\Parameter(name="user", in="path", required=true, type="integer"),
     *     @SWG\Parameter(name="page", in="query", type="number", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="number", default="15"),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/MessageResource"))
     *     ),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function messages(Request $request, $userId)
    {
        $messages = $this->repository
            ->history(\Auth::user(), $userId)
            ->paginate((int)$request->get('per_page', 15));

        return MessageResource::collection($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @SWG\Post(
     *     path="/client/chat/messages",
     *     summary="Store a newly created message in storage",
     *     tags={"Client: chat"},
     *     operationId="sendMessage",
     *     @SWG\Parameter(name="message", in="formData", type="string", required=true),
     *     @SWG\Parameter(name="recipient", in="formData", type="integer", description="Recipient id", required=true),
     *     @SWG\Response(response="200", description="Success", ref="#/definitions/MessageResource"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function sendMessage(StoreMessage $request)
    {
        $message = $this->repository->create($request->only('message'));

        $message->sender()->attach([
            \Auth::id() => ['to_user_id' => $request->recipient],
        ]);

        $message->load('users');

        return MessageResource::make($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->sendNotFoundResponse();
    }
}
