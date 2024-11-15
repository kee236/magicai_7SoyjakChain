<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AIEngine;
use App\Models\AiModel;
use Illuminate\Database\Seeder;

class AIModelSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAiModels();
    }

    private function createAiModels(): void
    {
        /** @formatter:off */
        $models = [
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'whisper-1', 'title' => 'The latest text to speech model, optimized for speed.'],
            // DALL·E image models
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'dall-e-2', 'title' => 'The previous DALL·E model released in Nov 2022.'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'dall-e-3', 'title' => 'The latest DALL·E model released in Nov 2023.'],
            // TTS voice models
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'tts-1', 'title' => 'The latest text to speech model, optimized for speed.'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'tts-1-hd', 'title' => 'The latest text to speech model, optimized for quality.'],
            // GPT text models
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-3.5-turbo-16k', 'title' => 'ChatGTP (3.5-turbo-16k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-3.5-turbo', 'title' => 'ChatGPT (Most Expensive & Fastest & Most Capable'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-3.5-turbo-0125', 'title' => 'ChatGTP (Updated Knowledge cutoff of Sep 2021, 16k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4', 'title' => 'ChatGPT-4 (Most Expensive & Fastest & Most Capable'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4-vision-preview', 'title' => 'GPT-4 Turbo with vision (Understand images, in addition to all other GPT-4 Turbo capabilities)'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4-1106-preview', 'title' => 'GPT-4 preview (Updated Knowledge cutoff of April 2023, 128k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4-0125-preview', 'title' => 'GPT-4 Turbo (Updated Knowledge cutoff of Dec 2023, 128k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4-turbo', 'title' => 'GPT-4 Turbo (Updated Knowledge cutoff of April 2023, 128k)'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4o', 'title' => 'GPT-4o Most advanced, multimodal flagship model that’s cheaper and faster than GPT-4 Turbo.  (Updated Knowledge cutoff of Oct 2023, 128k)'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'gpt-4o-mini', 'title' => 'Our affordable and intelligent small model for fast, lightweight tasks. GPT-4o mini is cheaper and more capable than GPT-3.5 Turbo.'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'o1-preview', 'title' => 'GPT o1-preview (Updated Knowledge cutoff of Oct 2023, 128k'],
            ['aiEngine' => AIEngine::OPEN_AI, 'key' => 'o1-mini', 'title' => 'GPT o1-mini (Updated Knowledge cutoff of Oct 2023, 128k'],
            // Anthropic
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-3-5-sonnet-20240620', 'title' => 'Claude 3.5 Sonnet'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-3-sonnet-20240229', 'title' => 'Claude 3 Sonnet'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-3-opus-20240229', 'title' => 'Claude 3 Opus'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-3-haiku-20240307', 'title' => 'Claude 3 Haiku'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-2.1', 'title' => 'Claude 2.1'],
            ['aiEngine' => AIEngine::ANTHROPIC, 'key' => 'claude-2.0', 'title' => 'Claude 2'],
            // Gemini
            ['aiEngine' => AIEngine::GEMINI, 'key' => 'gemini-1.5-pro-latest', 'title' => 'Gemini 1.5 Pro (Preview only) (Model last updated: April 2024)'],
            ['aiEngine' => AIEngine::GEMINI, 'key' => 'gemini-pro', 'title' => 'Gemini 1.0 Pro (Model last updated: February 2024)'],
            ['aiEngine' => AIEngine::GEMINI, 'key' => 'gemini-1.5-flash', 'title' => 'Gemini 1.0 Pro Vision (Model last updated: February 2023)'],
            // Unsplash
            ['aiEngine' => AIEngine::UNSPLASH, 'key' => 'unsplash', 'title' => 'Unsplash for AI Article Wizard'],
            // Pexels
            ['aiEngine' => AIEngine::PEXELS, 'key' => 'pexels', 'title' => 'Pexels for AI Article Wizard'],
            // Pixabay
            ['aiEngine' => AIEngine::PIXABAY, 'key' => 'pixabay', 'title' => 'Pixabay for AI Article Wizard'],
            // Elevenlabs
            ['aiEngine' => AIEngine::ELEVENLABS, 'key' => 'elevenlabs', 'title' => 'Elevenlabs for TTS'],
            ['aiEngine' => AIEngine::ELEVENLABS, 'key' => 'isolator', 'title' => 'Voice Isolator (1 word = 5 used characters of elevenlabs) X 1 token'],
            // Google
            ['aiEngine' => AIEngine::GOOGLE, 'key' => 'google', 'title' => 'Google for TTS'],
            // Azure
            ['aiEngine' => AIEngine::AZURE, 'key' => 'azure', 'title' => 'Azure for TTS'],
            // Serper
            ['aiEngine' => AIEngine::SERPER, 'key' => 'serper', 'title' => 'Serper for Realtime Data'],
            // Stable Diffusion
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'image-to-video', 'title' => 'AI Video'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'stable-diffusion-xl-1024-v0-9', 'title' => 'Stable Diffusion XL 0.9'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'stable-diffusion-xl-1024-v1-0', 'title' => 'Stable Diffusion XL 1.0'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'stable-diffusion-v1-6', 'title' => 'Stable Diffusion 1.6'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'stable-diffusion-xl-beta-v2-2-2', 'title' => 'Stable Diffusion 2.2.2 Beta'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'sd3', 'title' => 'Stable Diffusion 3'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'sd3-turbo', 'title' => 'Stable Diffusion 3 turbo'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'sd3-medium', 'title' => 'Stable Diffusion 3 Medium'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'sd3-large', 'title' => 'Stable Diffusion 3 Large'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'sd3-large-turbo', 'title' => 'Stable Diffusion 3 Large Turbo'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'core', 'title' => 'Stable Diffusion Core'],
            ['aiEngine' => AIEngine::STABLE_DIFFUSION, 'key' => 'ultra', 'title' => 'Stable Diffusion Ultra'],
            // Clipdrop
            ['aiEngine' => AIEngine::CLIPDROP, 'key' => 'clipdrop', 'title' => 'Clipdrop for Photo Studio'],
            // Plagiarism Check
            ['aiEngine' => AIEngine::PLAGIARISM_CHECK, 'key' => 'plagiarismcheck', 'title' => 'Plagiarism Check'],
            // Synthesia
            ['aiEngine' => AIEngine::SYNTHESIA, 'key' => 'synthesia', 'title' => 'Synthesia'],
            // Pebblely
            ['aiEngine' => AIEngine::PEBBLELY, 'key' => 'pebblely', 'title' => 'Pebblely'],

            ['aiEngine' => AIEngine::FAL_AI, 'key' => 'flux-pro', 'title' => 'Flux Pro'],
            ['aiEngine' => AIEngine::FAL_AI, 'key' => 'flux-realism', 'title' => 'Flux Realism Lora'],

        ];
        /** @formatter:on */
        foreach ($models as $model) {
            $this->createAiModel(...$model);
        }
    }

    private function createAiModel(AIEngine $aiEngine, string $key, string $title): void
    {
        AiModel::query()
            ->firstOrCreate([
                'key' => $key,
            ], [
                'ai_engine'      => $aiEngine,
                'key'            => $key,
                'title'          => $title,
                'is_active'      => true,
                'selected_title' => $title,
            ]);
    }
}
