<?php

namespace App\Repository;

use App\Document\GameResult;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use InvalidArgumentException;

class GameResultRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameResult::class);
    }

    /**
     * @param GameResult[] $documents
     *
     * @throws MongoDBException
     * @throws InvalidArgumentException
     */
    public function save(array $documents): void
    {
        foreach ($documents as $document) {
            if (!$document instanceof GameResult) {
                throw new InvalidArgumentException(sprintf('Only "%s" documents are supported', GameResult::class));
            }
            $this->getDocumentManager()->persist($document);
        }
        $this->getDocumentManager()->flush();
    }
}
