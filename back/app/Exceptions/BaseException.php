<?php

namespace App\Exceptions;

use App\Enums\JsonFieldNames;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseException extends Exception
{
    protected array $errors = [];

    public function __construct(string $message, array $errors, int $code = 0)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function render(Request $request): Response
    {
        return response([
            JsonFieldNames::MESSAGE->value => $this->message,
            JsonFieldNames::ERRORS->value => $this->errors,
        ],
            $this->getCode()
        );
    }
}
