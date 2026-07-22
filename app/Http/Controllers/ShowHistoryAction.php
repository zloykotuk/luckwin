<?php

namespace App\Http\Controllers;

use App\Data\LinkHashData;
use App\Http\Resources\HistoryResource;
use App\Models\Link;
use App\Services\HistoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShowHistoryAction extends Controller
{
    public function __invoke(Link $link, HistoryService $service): AnonymousResourceCollection
    {
        return HistoryResource::collection($service->latestForLink(new LinkHashData($link->hash)));
    }
}
