<?php

namespace App\Factory;

use App\Document\GameResult;

interface GameResultFactoryInterface
{
    /**
     * @param array $results
     *
     * @return GameResult[]
     */
    public function create(array $results): array;
}
