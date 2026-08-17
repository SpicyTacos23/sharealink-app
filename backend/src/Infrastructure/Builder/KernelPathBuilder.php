<?php

namespace App\Infrastructure\Builder;

class KernelPathBuilder
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getProjectPath(): string
    {
        return $this->projectDir;
    }
}
