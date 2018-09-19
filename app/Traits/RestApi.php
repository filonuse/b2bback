<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\Resource;

trait RestApi
{
    /**
     * Status code of response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Getter for statusCode
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Send success response
     *
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccessResponse($message = 'Success')
    {
        return response()->json(['message' => $message], $this->statusCode);
    }

    /**
     * Send custom data response
     *
     * @param $data
     * @param $status
     * @param $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCustomResponse($data = [], $status = 200, $headers = [])
    {
        return response()->json($data, $status, $headers);
    }

    /**
     * Send this response when api user provide incorrect data type for the field
     *
     * @param $errors
     * @return mixed
     */
    public function sendInvalidDataResponse($errors)
    {
        return response()->json((['message' => 'The given data was invalid.', 'errors' => $errors]), 422);
    }

    /**
     * Send this response when api user provide incorrect data credentials
     *
     * @param $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvalidCredentialsResponse($errors = [])
    {
        return response()->json(['message' => 'Invalid Credentials', 'errors' => $errors], 400);
    }

    /**
     * Send this response when a api user try access a resource that they don't belong
     *
     * @return string
     */
    public function sendForbiddenResponse()
    {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    /**
     * Send 404 not found response
     *
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    public function sendNotFoundResponse($message = 'The requested resource was not found')
    {
        return response()->json(['message' => $message], 404);
    }

    /**
     * Send empty data response
     *
     * @return string
     */
    public function sendEmptyDataResponse()
    {
        return response()->json(['data' => new \StdClass()]);
    }
}