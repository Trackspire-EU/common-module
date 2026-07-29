<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Validator;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Trackspire\CommonModule\Exception\RequestValidationException;

class RequestValidator
{
    public const string LOCATION_QUERY = 'query';
    public const string LOCATION_BODY = 'body';

    public function validate(
        ServerRequestInterface $request,
        array $rules
    ): void {
        $queryParams = $request->getQueryParams();
        $bodyParams  = $request->getParsedBody();

        foreach ($rules as $param => $rule) {
            $isRequired = $rule['required'] ?? false;
            $location   = $rule['location'] ?? self::LOCATION_QUERY;

            $value = $location === self::LOCATION_BODY
                ? ($bodyParams[$param] ?? null)
                : ($queryParams[$param] ?? null);

            if ($isRequired && ($value === null || $value === '')) {
                throw new RequestValidationException(
                    "Parameter '{$param}' is required in {$location}.",
                    StatusCodeInterface::STATUS_BAD_REQUEST
                );
            }

            if ($value === null) {
                continue;
            }

            if (!empty($rule['email']) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new RequestValidationException(
                    "Parameter '{$param}' must be a valid email address.",
                    StatusCodeInterface::STATUS_BAD_REQUEST
                );
            }

            if (isset($rule['min_length']) && strlen((string) $value) < $rule['min_length']) {
                throw new RequestValidationException(
                    "Parameter '{$param}' must be at least {$rule['min_length']} characters.",
                    StatusCodeInterface::STATUS_BAD_REQUEST
                );
            }

            if (isset($rule['max_length']) && strlen((string) $value) > $rule['max_length']) {
                throw new RequestValidationException(
                    "Parameter '{$param}' must not exceed {$rule['max_length']} characters.",
                    StatusCodeInterface::STATUS_BAD_REQUEST
                );
            }

            if (!empty($rule['pattern']) && !preg_match($rule['pattern'], (string) $value)) {
                throw new RequestValidationException(
                    "Parameter '{$param}' has an invalid format.",
                    StatusCodeInterface::STATUS_BAD_REQUEST
                );
            }
        }
    }
}
