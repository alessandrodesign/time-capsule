<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected Response $response;

    public function __construct(
        protected Request $request
    )
    {
        $this->response = new Response();
    }

    protected function notFound(string $message): JsonResponse
    {
        return $this->fail($message, null, Response::HTTP_NOT_FOUND);
    }

    protected function fail(string $message, ?array $errors = null, $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'statusCode' => $code,
            'error' => $message,
            'errors' => $errors,
        ]);
    }

    protected function created(string $message, array $data = null): JsonResponse
    {
        return $this->respond($message, $data, Response::HTTP_CREATED);
    }

    protected function deleted(string $message): JsonResponse
    {
        return $this->respond($message, null, Response::HTTP_NO_CONTENT);
    }

    protected function respond(string $message, array $data = null, $code = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'statusCode' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}