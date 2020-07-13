<?php

namespace App\Validator\Api\Exception;

use Exception;
use Throwable;

class InvalidResponseException extends Exception
{
    /**
     * @var array
     */
    private $responseBody;

    public function __construct(array $responseBody, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }
}
