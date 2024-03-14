<?php

namespace App\Contracts;

use Illuminate\Contracts\Routing\ResponseFactory as BaseResponseFactory;

interface ResponseFactory extends BaseResponseFactory
{
    /**
     * Return a new JSON response from the application.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function api($data, $success = true, $message = null, $status = 200);
}
