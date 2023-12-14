<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

final readonly class Text implements Set
{
    public function __construct(
        public string $text,
    ) {
    }

    public function type(): SetType
    {
        return SetType::Text;
    }
}
