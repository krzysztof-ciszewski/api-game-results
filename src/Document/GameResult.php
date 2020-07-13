<?php

namespace App\Document;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\OrderFilter;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ApiResource(collectionOperations={"get"}, itemOperations={"get"})
 * @ApiFilter(OrderFilter::class, properties={"score", "finishedAt"}, arguments={"orderParameterName"="order"})
 *
 * @ODM\Document(repositoryClass="App\Repository\GameResultRepository")
 */
class GameResult
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    private $id;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    private $score;

    /**
     * @var User
     * @ODM\EmbedOne(User::class)
     */
    private $user;

    /**
     * @var DateTime
     * @ODM\Field(type="date")
     */
    private $finishedAt;

    /**
     * @var DateTime|null
     * @Gedmo\Timestampable(on="update")
     * @ODM\Field(type="date")
     */
    private $updatedAt = null;

    public function __construct(string $id, int $score, User $user, DateTime $finishedAt)
    {
        $this->id = $id;
        $this->score = $score;
        $this->user = $user;
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getFinishedAt(): DateTime
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTime $finishedAt
     */
    public function setFinishedAt(DateTime $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
