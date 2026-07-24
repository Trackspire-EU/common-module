<?php

namespace Trackspire\CommonModule\Value\Result;

class JsonResult implements ResultInterface
{
    private function __construct(
        private readonly string $message,
        private ?array          $fields = null,
    ) {
    }

    public static function from(
        string $message,
        ?array $fields = null,
    ): self {
        return new self($message, $fields);
    }

    public function addField(string|int $key, mixed $value): void
    {
        $this->fields[$key] = $value;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'data' => $this->fields,
        ];
    }
}
