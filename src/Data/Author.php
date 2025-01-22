<?php

namespace Spatie\ContentApi\Data;

final readonly class Author
{
    public function __construct(
        public string $name,
        public string $gravatar_url,
    ) {}

    public static function fromResponse(array $author): self
    {
        return new self(
            name: $author['name'],
            gravatar_url: 'https://www.gravatar.com/avatar/'.md5($author['email']),
        );
    }
}
