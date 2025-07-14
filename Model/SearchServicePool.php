<?php

declare(strict_types=1);

namespace Sandip\SearchOverlay\Model;

class SearchServicePool
{
    private $services;

    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    public function getServices()
    {
        return $this->services;
    }
}
