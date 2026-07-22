<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkResource;
use App\Models\Link;
use Inertia\Inertia;
use Inertia\Response;

class ShowLinkAction extends Controller
{
    public function __invoke(Link $link): Response
    {
        return Inertia::render('PageA', LinkResource::make($link)->resolve());
    }
}
