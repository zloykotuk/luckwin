<?php

namespace App\Http\Controllers;

use App\Data\LinkHashData;
use App\Http\Resources\LuckyResultResource;
use App\Models\Link;
use App\Services\LuckyService;

class PlayLuckyAction extends Controller
{
    public function __invoke(Link $link, LuckyService $service): LuckyResultResource
    {
        return LuckyResultResource::make($service->play(new LinkHashData($link->hash)));
    }
}
