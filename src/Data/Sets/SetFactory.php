<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

class SetFactory
{
    public static function fromResponse(array $response): Set
    {
        return match ($response['type']) {
            SetType::Text->value => new Text($response['text']),
            SetType::Code->value => new Code($response['code'], $response['caption']),
            SetType::Video->value => new Video(
                video: $response['video'],
                embedUrl: $response['embed_url'],
                videoId: $response['video_id'],
                autoplay: $response['autoplay'],
                loop: $response['autoloop'],
            ),
        };
    }
}
