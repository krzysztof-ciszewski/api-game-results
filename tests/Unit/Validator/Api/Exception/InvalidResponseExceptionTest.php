<?php

namespace App\Tests\Unit\Validator\Api\Exception;

use App\Validator\Api\Exception\InvalidResponseException;
use PHPUnit\Framework\TestCase;

class InvalidResponseExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new InvalidResponseException(['some' => 'data']);
        $this->assertSame(['some' => 'data'], $exception->getResponseBody());
    }
}
