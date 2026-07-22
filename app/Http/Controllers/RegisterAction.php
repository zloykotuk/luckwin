<?php

namespace App\Http\Controllers;

use App\Data\RegisterData;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\LinkResource;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;

class RegisterAction extends Controller
{
    public function __invoke(RegisterRequest $request, RegistrationService $service): JsonResponse
    {
        $link = $service->register(RegisterData::fromRequest($request));

        return LinkResource::make($link)->response()->setStatusCode(201);
    }
}
