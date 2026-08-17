<?php

namespace App\Application\UseCase\MediaFile;

class ListMediaFileHandler
{
    public function __construct(private readonly ListMediaFileService $service) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(ListMediaFileRequest $request): array
    {
        return $this->service->list($request->page, $request->limit);
    }
}
