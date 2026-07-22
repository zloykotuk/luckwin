<?php

namespace App\Http\Controllers;

use App\Data\LinkHashData;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Services\LinkService;
use Illuminate\Http\Response;

class LinkController extends Controller
{
    public function regenerate(Link $link, LinkService $service): LinkResource
    {
        return LinkResource::make($service->regenerate(new LinkHashData($link->hash)));
    }

    public function destroy(Link $link, LinkService $service): Response
    {
        $service->deactivate(new LinkHashData($link->hash));

        return response()->noContent();
    }
}
