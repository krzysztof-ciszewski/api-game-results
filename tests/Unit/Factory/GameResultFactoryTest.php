<?php

namespace App\Tests\Unit\Factory;

use App\Document\GameResult;
use App\Factory\GameResultFactory;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class GameResultFactoryTest extends TestCase
{
    /**
     * @dataProvider createValidDataProvider
     */
    public function testCreateValidData(array $results): void
    {
        $factory = new GameResultFactory();
        /** @var GameResult[] $documents */
        $documents = $factory->create($results);
        if (empty($results)) {
            $this->assertEmpty($documents);
        }
        foreach ($results as $i => $result) {
            $this->assertSame($result['id'], $documents[$i]->getId());
            $this->assertSame($result['score'], $documents[$i]->getScore());
            $this->assertEquals(
                DateTime::createFromFormat(DateTime::RFC3339, $result['finished_at']),
                $documents[$i]->getFinishedAt()
            );
            $this->assertSame($result['user']['id'], $documents[$i]->getUser()->getId());
            $this->assertSame($result['user']['name'], $documents[$i]->getUser()->getName());
        }
    }

    /**
     * @dataProvider createInvalidDataProvider
     */
    public function testCreateInvalidData(array $results, string $expectedExceptionClass): void
    {
        $this->expectException($expectedExceptionClass);
        (new GameResultFactory())->create($results);
    }

    public function createValidDataProvider(): array
    {
        return [
            [
                [
                    [
                        'id' => 'id',
                        'score' => 1,
                        'finished_at' => '2020-02-27T11:25:00+00:00',
                        'user' => ['id' => 'id', 'name' => 'name'],
                    ],
                ],
            ],
            [
                [],
            ],
        ];
    }

    public function createInvalidDataProvider(): array
    {
        return [
            'missing required keys' => [
                [
                    [
                        'score' => 1,
                        'user' => ['id' => 'id', 'name' => 'name'],
                    ],
                ],
                InvalidArgumentException::class,
            ],
            'invalid date format' => [
                [
                    [
                        'id' => 'id',
                        'score' => 1,
                        'finished_at' => 'date',
                        'user' => ['id' => 'id', 'name' => 'name'],
                    ],
                ],
                TypeError::class,
            ],
        ];
    }
}
