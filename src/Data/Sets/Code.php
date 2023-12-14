<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

final readonly class Code implements Set
{
    public function __construct(
        public string $code,
        public ?string $caption,
    ) {
    }

    public function type(): SetType
    {
        return SetType::Code;
    }
}
