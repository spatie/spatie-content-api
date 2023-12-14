<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

final readonly class Video implements Set
{
    public function __construct(
        public string $video,
        public string $embedUrl,
        public ?string $videoId,
    )
    {
    }

    public function type(): SetType
    {
        return SetType::Video;
    }
}
