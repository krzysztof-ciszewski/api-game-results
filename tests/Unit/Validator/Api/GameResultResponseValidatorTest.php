<?php

namespace App\Tests\Unit\Validator\Api;

use App\Validator\Api\Exception\InvalidResponseException;
use App\Validator\Api\GameResultResponseValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameResultResponseValidatorTest extends TestCase
{
    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate(
        array $data,
        Constraint $constraint,
        ConstraintViolationListInterface $violationList,
        string $expectedExceptionClass = null
    ): void {
        if ($expectedExceptionClass) {
            $this->expectException($expectedExceptionClass);
        }
        $validator = new GameResultResponseValidator($this->getValidatorMock($data, $constraint, $violationList));
        $validator->validate($data);
    }

    public function validateDataProvider(): array
    {
        return [
            'valid' => [
                ['some' => 'data'],
                new All(
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
                ),
                new ConstraintViolationList(),
            ],
            'invalid' => [
                ['some' => 'data'],
                new All(
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
                ),
                $this->getListWithErrors(),
                InvalidResponseException::class,
            ],
        ];
    }

    private function getValidatorMock(
        array $response,
        Constraint $constraint,
        ConstraintViolationListInterface $violationList,
        int $validateCalls = 1
    ): ValidatorInterface {
        $mock = $this->prophesize(ValidatorInterface::class);
        $mock->validate($response, $constraint)->willReturn($violationList)->shouldBeCalledTimes($validateCalls);

        return $mock->reveal();
    }

    private function getListWithErrors(int $errors = 1): ConstraintViolationListInterface
    {
        $mock = $this->prophesize(ConstraintViolationListInterface::class);
        $mock->count()->willReturn($errors);

        return $mock->reveal();
    }
}
