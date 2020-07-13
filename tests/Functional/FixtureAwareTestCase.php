<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\Bundle\MongoDBBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

abstract class FixtureAwareTestCase extends ApiTestCase
{
    /**
     * @var MongoDBExecutor
     */
    private $fixtureExecutor;

    /**
     * @var ContainerAwareLoader
     */
    private $fixtureLoader;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function addFixture(FixtureInterface $fixture): void
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    protected function executeFixtures(): void
    {
        $this->getFixtureExecutor()->execute($this->getFixtureLoader()->getFixtures());
    }

    private function getFixtureExecutor(): MongoDBExecutor
    {
        if (!$this->fixtureExecutor) {
            $documentManager = self::$kernel->getContainer()->get('doctrine_mongodb')->getManager();
            $this->fixtureExecutor = new MongoDBExecutor($documentManager, new MongoDBPurger($documentManager));
        }

        return $this->fixtureExecutor;
    }

    private function getFixtureLoader(): ContainerAwareLoader
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new SymfonyFixturesLoader(self::$kernel->getContainer());
        }

        return $this->fixtureLoader;
    }
}
