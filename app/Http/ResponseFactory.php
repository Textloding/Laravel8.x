<?php

namespace App\Http;

use Illuminate\Routing\ResponseFactory as BaseResponseFactory;

class ResponseFactory extends BaseResponseFactory implements \App\Contracts\ResponseFactory
{
    public function api($data, $success = true, $message = null, $status = 200)
    {
        return $this->json([
            'status' => $status,
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
