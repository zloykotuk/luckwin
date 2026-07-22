<?php

namespace App\Services;

use App\Data\LinkHashData;
use App\Models\Link;
use App\Repositories\LinkRepository;

final readonly class LinkService
{
    public function __construct(private LinkRepository $links) {}

    public function regenerate(LinkHashData $data): Link
    {
        return $this->links->regenerateHash($data->hash);
    }

    public function deactivate(LinkHashData $data): void
    {
        $this->links->deactivate($data->hash);
    }
}
