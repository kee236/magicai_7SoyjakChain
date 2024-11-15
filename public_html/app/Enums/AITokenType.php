<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum AITokenType: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case WORD = 'word';

    case IMAGE = 'image';

    public function label(): string
    {
        return match ($this) {
            self::WORD   => __('Word'),
            self::IMAGE  => __('Image'),
        };
    }
}
