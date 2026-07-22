<?php

namespace App\Services;

use App\Data\LinkHashData;
use App\Models\History;
use App\Repositories\HistoryRepository;
use App\Repositories\LinkRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class HistoryService
{
    public function __construct(
        private LinkRepository $links,
        private HistoryRepository $history,
    ) {}

    /**
     * @return Collection<int, History>
     */
    public function latestForLink(LinkHashData $data): Collection
    {
        return $this->history->forUser($this->links->userIdForHash($data->hash));
    }
}
