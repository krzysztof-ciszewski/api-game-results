<?php

namespace App\Validator\Api;

use App\Validator\Api\Exception\InvalidResponseException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameResultResponseValidator implements GameResultResponseValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $response): void
    {
        $errors = $this->validator->validate($response, $this->buildConstraint());
        if ($errors->count() > 0) {
            throw new InvalidResponseException(iterator_to_array($errors, true));
        }
    }

    private function buildConstraint(): Constraint
    {
        return new All(
            [
                new Collection(
                    [
                        'id' => new Uuid(),
                        'user' => new Collection(
                            [
                                'id' => new Uuid(),
                                'name' => [new NotBlank(), new Type('string')],
                            ]
                        ),
                        'finished_at' => new DateTime(['format' => \DateTime::RFC3339]),
                        'score' => new Type('integer'),
                    ]
                ),
            ]
        );
    }
}
