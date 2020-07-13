<?php

namespace App\Tests\Unit\Document;

use App\Document\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @dataProvider settersDataProvider
     */
    public function testSetters(string $id, string $name): void
    {
        $newId = $id.'1';
        $newName = $name.'1';
        $document = new User($id, $name);
        $document->setId($newId);
        $document->setName($newName);
        $this->assertSame($newId, $document->getId());
        $this->assertSame($newName, $document->getName());
    }

    public function settersDataProvider(): array
    {
        return [
            ['id', 'name'],
        ];
    }
}
