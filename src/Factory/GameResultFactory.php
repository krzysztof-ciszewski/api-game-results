<?php

namespace App\Factory;

use App\Document\GameResult;
use App\Document\User;
use DateTime;
use InvalidArgumentException;

class GameResultFactory implements GameResultFactoryInterface
{
    public function create(array $results): array
    {
        $entities = [];
        foreach ($results as $result) {
            if (!isset($result['id'], $result['score'], $result['finished_at'], $result['user']['id'], $result['user']['name'])) {
                throw new InvalidArgumentException('Missing required keys');
            }
            $entities[] = new GameResult(
                $result['id'],
                $result['score'],
                new User($result['user']['id'], $result['user']['name']),
                DateTime::createFromFormat(DateTime::RFC3339, $result['finished_at'])
            );
        }

        return $entities;
    }
}
