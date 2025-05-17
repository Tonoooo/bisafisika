<?php

namespace App\Traits;

trait HasRepeaterIndex
{
    public function getRepeaterIndex(string $repeaterName): int
    {
        $path = $this->getStatePath();
        if (preg_match("/{$repeaterName}\.(\d+)/", $path, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }
}