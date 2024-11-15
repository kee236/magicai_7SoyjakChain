<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum Introduction: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case INITIALIZE = 'initialize';
    case AFFILIATE_SEND = 'affiliate_send';
    case SELECT_PLAN = 'select_plan';
    case AI_WRITER = 'ai_writer';
    case AI_IMAGE = 'ai_image';
    case AI_PDF = 'ai_pdf';
    case AI_CODE = 'ai_code';

    public function label(): string
    {
        return match ($this) {
            self::INITIALIZE     => __('Introduction'),
            self::AFFILIATE_SEND => __('Affiliate'),
            self::SELECT_PLAN    => __('Select Plan'),
            self::AI_WRITER      => __('AI Writer'),
            self::AI_IMAGE       => __('AI Image'),
            self::AI_PDF         => __('AI File Chat'),
            self::AI_CODE        => __('AI Code'),
        };
    }
}
