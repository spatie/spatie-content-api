<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

final readonly class Video implements Set
{
    public function __construct(
        public string $video,
        public string $embedUrl,
        public ?string $videoId,
        public bool $autoplay = false,
        public bool $loop = false,
    ) {
    }

    public function type(): SetType
    {
        return SetType::Video;
    }
}
