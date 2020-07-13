<?php

namespace App\Tests\Unit\Message;

use App\Message\UpdateGameResults;
use PHPUnit\Framework\TestCase;

class UpdateGameResultsTest extends TestCase
{
    public function testConstruct(): void
    {
        $this->assertInstanceOf(UpdateGameResults::class, new UpdateGameResults());
    }
}
