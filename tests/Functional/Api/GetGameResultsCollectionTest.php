<?php

namespace App\Tests\Functional\Api;

use App\DataFixtures\GameResultFixture;
use App\DataFixtures\GameResultUpdatedYesterdayFixture;
use App\Document\GameResult;
use App\Tests\Functional\FixtureAwareTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetGameResultsCollectionTest extends FixtureAwareTestCase
{
    public function testGetCollectionNoApiRefresh(): void
    {
        $this->addFixture(new GameResultFixture());
        $this->executeFixtures();
        $response = static::createClient()->request(
            Request::METHOD_GET,
            '/api/game_results',
            ['headers' => ['Accept' => 'application/json']]
        );
        self::assertResponseIsSuccessful();
        $gameResults = $response->toArray();
        usort(
            $gameResults,
            static function ($left, $right) {
                return filter_var($left['id'], FILTER_SANITIZE_NUMBER_INT)
                    <=> filter_var($right['id'], FILTER_SANITIZE_NUMBER_INT);
            }
        );
        $this->assertCount(10, $gameResults);
        self::assertMatchesResourceCollectionJsonSchema(GameResult::class, null, 'json');
        foreach ($gameResults as $index => $gameResult) {
            self::assertArraySubset(
                ['id' => 'id'.($index + 1), 'score' => 5, 'user' => ['id' => 'id'.($index + 1), 'name' => 'name']],
                $gameResult
            );
            $this->assertSame(
                \DateTime::createFromFormat(\DateTime::RFC3339, $gameResult['finishedAt'])->format(\DateTime::RFC3339),
                $gameResult['finishedAt']
            );
            $this->assertSame(
                \DateTime::createFromFormat(\DateTime::RFC3339, $gameResult['updatedAt'])->format(\DateTime::RFC3339),
                $gameResult['updatedAt']
            );
        }
    }

    public function testGetCollectionWithApiRefresh(): void
    {
        $this->markTestIncomplete();
        $this->addFixture(new GameResultUpdatedYesterdayFixture());
        $this->executeFixtures();
        $client = static::createClient();
        $response = $client->request(
            Request::METHOD_GET,
            '/api/game_results',
            ['headers' => ['Accept' => 'application/json']]
        );
        self::assertResponseIsSuccessful();
        $results = $response->toArray();
        $this->assertCount(10, $results);
        self::assertMatchesResourceCollectionJsonSchema(GameResult::class, null, 'json');
    }
}
