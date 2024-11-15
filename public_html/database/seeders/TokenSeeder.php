<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AIEngine;
use App\Enums\AITokenType;
use App\Models\AiModel;
use Illuminate\Database\Seeder;

class TokenSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAllTokens();
    }

    private function createAllTokens(): void
    {
        /** @formatter:off */
        $models = [
            // openai
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'whisper-1'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::IMAGE, 'key' => 'dall-e-2'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::IMAGE, 'key' => 'dall-e-3'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'tts-1'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'tts-1-hd'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-3.5-turbo-16k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-3.5-turbo'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-3.5-turbo-0125'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4-vision-preview'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4-1106-preview'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4-0125-preview'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4-turbo'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4o'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'gpt-4o-mini'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'o1-preview'],
            ['aiEngine' => AIEngine::OPEN_AI, 'type' => AITokenType::WORD, 'key' => 'o1-mini'],
            // anthropic
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-3-5-sonnet-20240620'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-3-sonnet-20240229'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-3-opus-20240229'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-3-haiku-20240307'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-2.1'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'type' => AITokenType::WORD, 'key' => 'claude-2.0'],
            // gemini
            ['aiEngine' => AIEngine::GEMINI, 'type' => AITokenType::WORD, 'key' => 'gemini-1.5-pro-latest'],
            ['aiEngine' => AIEngine::GEMINI, 'type' => AITokenType::WORD, 'key' => 'gemini-pro'],
            ['aiEngine' => AIEngine::GEMINI, 'type' => AITokenType::WORD, 'key' => 'gemini-1.5-flash'],
            // unsplash
            ['aiEngine' => AIEngine::UNSPLASH, 'type' => AITokenType::IMAGE, 'key' => 'unsplash'],
            // pexels
            ['aiEngine' => AIEngine::PEXELS, 'type' => AITokenType::IMAGE, 'key' => 'pexels'],
            // pixabay
            ['aiEngine' => AIEngine::PIXABAY, 'type' => AITokenType::IMAGE, 'key' => 'pixabay'],
            // elevenlabs
            ['aiEngine' => AIEngine::ELEVENLABS, 'type' => AITokenType::WORD, 'key' => 'elevenlabs'],
            ['aiEngine' => AIEngine::ELEVENLABS, 'type' => AITokenType::WORD, 'key' => 'isolator'],
            // google
            ['aiEngine' => AIEngine::GOOGLE, 'type' => AITokenType::WORD, 'key' => 'google'],
            // azure
            ['aiEngine' => AIEngine::AZURE, 'type' => AITokenType::WORD, 'key' => 'azure'],
            // serper
            ['aiEngine' => AIEngine::SERPER, 'type' => AITokenType::WORD, 'key' => 'serper'],
            // stable diffusion
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'image-to-video'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'stable-diffusion-xl-1024-v0-9'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'stable-diffusion-xl-1024-v1-0'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'stable-diffusion-v1-6'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'stable-diffusion-xl-beta-v2-2-2'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'sd3'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'sd3-turbo'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'sd3-medium'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'sd3-large'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'sd3-large-turbo'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'core'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'type' => AITokenType::IMAGE, 'key' => 'ultra'],
            // clipdrop
            ['aiEngine' => AIEngine::CLIPDROP, 'type' => AITokenType::IMAGE, 'key' => 'clipdrop'],
            // plagiarism check
            ['aiEngine' => AIEngine::PLAGIARISM_CHECK, 'type' => AITokenType::WORD, 'key' => 'plagiarismcheck'],
            // synthesia
            ['aiEngine' => AIEngine::SYNTHESIA, 'type' => AITokenType::IMAGE, 'key' => 'synthesia'],
        ];
        /** @formatter:on */
        foreach ($models as $model) {
            $this->createToken(...$model);
        }
    }

    private function createToken(AIEngine $aiEngine, AITokenType $type, string $key): void
    {
        $aiModel = AiModel::query()
            ->where('ai_engine', $aiEngine)
            ->where('key', $key)
            ->firstOrFail();

        $defaultToken = 1.00;

        if ($aiEngine == AIEngine::SYNTHESIA) {
            $defaultToken = 20.00;
        }

        $aiModel->tokens()->firstOrCreate(
            [
                'ai_model_id' => $aiModel->id,
            ],
            [
                'type'           => $type,
                'cost_per_token' => $defaultToken,
            ]
        );
    }
}
