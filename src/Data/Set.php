<?php

namespace Spatie\ContentApi\Data;

final readonly class Set
{
    public function __construct(
        public SetType $type,
        public ?string $text,
        public ?string $code,
        public ?string $caption,
    ) {
    }

    public static function fromResponse(array $set): self
    {
        return new self(
            type: SetType::from($set['type']),
            text: $set['text'] ?? null,
            code: $set['code'] ?? null,
            caption: $set['caption'] ?? null,
        );
    }
}
