<?php

namespace App\Services\Ai;

use App\Helpers\Classes\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FalAI
{
    public static function generate($prompt, string $model = 'flux-pro')
    {
        $model = 'fal-ai/' . (setting('fal_ai_default_model') ?: $model);

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . Helper::setFalAIKey(),
        ])
            ->post('https://queue.fal.run/' . $model, [
                'prompt' => $prompt,
            ]);

        if ($http->status() == 200) {
            if ($request_id = $http->json('request_id')) {
                return $request_id;
            }
        }

        return null;
    }

    public static function check($uuid, string $model = 'flux-pro')
    {
        $model = 'fal-ai/' . (setting('fal_ai_default_model') ?: $model);

        $url = 'https://queue.fal.run/' . $model . '/requests/' . $uuid;

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . Helper::setFalAIKey(),
        ])
            ->get($url);

        if ($images = $http->json('images')) {
            if (is_array($images)) {
                $image = Arr::first($images);

                return [
                    'image' => $image,
                    'size'  => data_get($image, 'width') . 'x' . data_get($image, 'height'),
                ];
            }
        }

        return null;
    }
}
