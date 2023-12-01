<?php

namespace Spatie\ContentApi\Data;

enum SetType: string
{
    case Text = 'text';
    case Code = 'code';
    case Video = 'video';
}
