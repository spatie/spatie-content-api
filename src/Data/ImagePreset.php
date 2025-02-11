<?php

namespace Spatie\ContentApi\Data;

class ImagePreset
{

    public function __construct(
        public string $name,
        public string $url,
        public int $width,
        public int $height,
    ) {
    }

    public static function fromResponse(string $name, array $imagePreset): self
    {
        return new self(
            name: $name,
            url: $imagePreset['url'],
            width: $imagePreset['width'],
            height: $imagePreset['height'],
        );
    }
}
