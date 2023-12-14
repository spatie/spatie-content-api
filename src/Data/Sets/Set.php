<?php

namespace Spatie\ContentApi\Data\Sets;

use Spatie\ContentApi\Data\SetType;

interface Set
{
    public function type(): SetType;
}
