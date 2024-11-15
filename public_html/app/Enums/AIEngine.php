<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum AIEngine: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case OPEN_AI = 'openai';

    case STABLE_DIFFUSION = 'stable_diffusion';

    case ANTHROPIC = 'anthropic';

    case GEMINI = 'gemini';

    case UNSPLASH = 'unsplash';

    case PEXELS = 'pexels';

    case PIXABAY = 'pixabay';

    case ELEVENLABS = 'elevenlabs';

    case GOOGLE = 'google';

    case AZURE = 'azure';

    case SERPER = 'serper';

    case CLIPDROP = 'clipdrop';

    case PLAGIARISM_CHECK = 'plagiarism_check';

    case SYNTHESIA = 'synthesia';
    case PEBBLELY = 'pebblely';

    case FAL_AI = 'fal_ai';

    public function label(): string
    {
        return match ($this) {
            self::OPEN_AI          => __('OpenAI'),
            self::STABLE_DIFFUSION => __('Stable Diffusion'),
            self::ANTHROPIC        => __('Anthropic'),
            self::GEMINI           => __('Gemini'),
            self::UNSPLASH         => __('Unsplash'),
            self::PEXELS           => __('Pexels'),
            self::PIXABAY          => __('Pixabay'),
            self::ELEVENLABS       => __('Elevenlabs'),
            self::GOOGLE           => __('Google TTS'),
            self::AZURE            => __('Azure TTS'),
            self::SERPER           => __('Serper'),
            self::CLIPDROP         => __('Clipdrop'),
            self::PLAGIARISM_CHECK => __('Plagiarism Check'),
            self::SYNTHESIA        => __('Synthesia'),
            self::PEBBLELY         => __('Pebblely'),
        };
    }
}
