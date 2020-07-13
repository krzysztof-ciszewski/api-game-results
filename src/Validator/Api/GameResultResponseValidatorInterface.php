<?php

namespace App\Validator\Api;

use App\Validator\Api\Exception\InvalidResponseException;

interface GameResultResponseValidatorInterface
{
    /**
     * @param array $response
     *
     * @throws InvalidResponseException
     */
    public function validate(array $response): void;
}
