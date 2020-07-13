<?php

namespace App\DataFixtures;

use App\Document\GameResult;
use App\Document\User;
use DateTime;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameResultUpdatedYesterdayFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $index) {
            $gameResult = new GameResult('id'.$index, 5, new User('id'.$index, 'name'), new DateTime());
            $gameResult->setUpdatedAt(new DateTime('yesterday'));
            $manager->persist($gameResult);
        }
        $manager->flush();
    }
}
