<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\UserOpenai;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

class AIImageController extends Controller
{
    protected $client;

    protected $settings;

    protected $settings_two;

    public const STABLEDIFFUSION = 'stablediffusion';

    public const STORAGE_S3 = 's3';

    public const STORAGE_LOCAL = 'public';

    public function __construct()
    {
        //Settings
        $this->settings = Setting::first();
        $this->settings_two = SettingTwo::first();
        if ($this->settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $this->settings?->openai_api_secret);
        }
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
        set_time_limit(120);
    }

    /**
     * Get Model Versions for AI Image Generation
     *
     * @OA\Get(
     *      path="/api/aiimage/versions",
     *      operationId="versions",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Get Model Versions for AI Image Generation (DALL-E, Stable Diffusion) from settings",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function versions()
    {
        return response()->json([
            'dall-e'           => $this->settings_two->dalle,
            'stable-diffusion' => $this->settings_two->stablediffusion_default_model,
        ]);
    }

    /**
     * Check if image generation is active
     *
     * @OA\Get(
     *      path="/api/aiimage/check-availability",
     *      operationId="checkActiveGeneration",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Check if image generation is active / available",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Image generation is available.",
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Image generation in progress. Please try again later.",
     *      ),
     * )
     */
    public function checkActiveGeneration()
    {
        $lockKey = 'generate_image_lock';

        // Attempt to acquire lock
        if (! Cache::lock($lockKey, 10)->get()) {
            // Failed to acquire lock, another process is already running
            return response()->json([
                'status'  => 'error',
                'message' => 'Image generation in progress. Please try again later.',
            ], 409);
        }

        // Release the lock
        Cache::lock($lockKey)->forceRelease();

        return response()->json([
            'status'  => 'success',
            'message' => 'Image generation is available.',
        ], 200);
    }

    /**
     * Generate Image
     *
     * @OA\Post(
     *      path="/api/aiimage/generate-image",
     *      operationId="generateImage",
     *      tags={"AI Image Generation"},
     *      security={{ "passport": {} }},
     *      summary="Generate Image (DALL-E / Stable Diffusion parameters required in request)",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Image generation successful. Image info in json response [images].",
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Image generation in progress. Please try again later.",
     *      ),
     * )
     */
    public function generateImage(Request $request)
    {

        $imageParam = $request->all();
        $post_type = 'ai_image_generator';
        $post = OpenAIGenerator::where('slug', $post_type)->first();
        $user = Auth::user();

        return $this->imageOutput($imageParam, $post, $user);

    }

    // This functions is copied from app/Http/Controllers/AIController.php as it is
    public function imageOutput($param, $post, $user)
    {
        $lockKey = 'generate_image_lock';

        // Attempt to acquire lock
        if (! Cache::lock($lockKey, 10)->get()) {
            // Failed to acquire lock, another process is already running
            return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
        }

        try {
            $user = Auth::user();
            // check daily limit
            $chkLmt = Helper::checkImageDailyLimit();
            if ($chkLmt->getStatusCode() === 429) {
                return $chkLmt;
            }
            // check remainings
            $chkImg = Helper::checkRemainingImages($user);
            if ($chkImg->getStatusCode() === 429) {
                return $chkImg;
            }

            if ($this->settings?->user_api_option) {
                $apiKeys = explode(',', auth()->user()?->api_keys);
            } else {
                $apiKeys = explode(',', $this->settings?->openai_api_secret);
            }
            $apiKey = $apiKeys[array_rand($apiKeys)];
            config(['openai.api_key' => $apiKey]);
            set_time_limit(120);

            //save generated image datas
            $entries = [];
            $prompt = '';
            $image_generator = $param['image_generator'];
            $number_of_images = (int) $param['image_number_of_images'];
            $mood = $param['image_mood'];

            if ($image_generator != self::STABLEDIFFUSION) {
                $size = $param['size'];
                $description = $param['description'];
                $prompt = "$description";
                $style = $param['image_style'];
                $lighting = $param['image_lighting'];
                // $image_model = $param['image_model'];

                if ($style != null) {
                    $prompt .= ' ' . $style . ' style.';
                }
                if ($lighting != null) {
                    $prompt .= ' ' . $lighting . ' lighting.';
                }
                if ($mood != null) {
                    $prompt .= ' ' . $mood . ' mood.';
                }
            } else {
                $stable_type = $param['type'];
                $prompt = $param['stable_description'];
                $negative_prompt = $param['negative_prompt'];
                $style_preset = $param['style_preset'];
                $sampler = $param['sampler'];
                $clip_guidance_preset = $param['clip_guidance_preset'];
                $image_resolution = $param['image_resolution'];
                $init_image = $param['image_src'] ?? null;
            }

            $image_storage = $this->settings_two->ai_image_storage;

            for ($i = 0; $i < $number_of_images; $i++) {
                if ($image_generator != self::STABLEDIFFUSION) {
                    //send prompt to openai
                    if ($prompt == null) {
                        return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
                    }
                    if ($this->settings_two->dalle == 'dalle2') {
                        $model = 'dall-e-2';
                        $demosize = '256x256'; // smallest size for demo
                    } elseif ($this->settings_two->dalle == 'dalle3') {
                        $model = 'dall-e-3';
                        $demosize = '1024x1024'; // smallest size for demo
                    } else {
                        $model = 'dall-e-2';
                        $demosize = '256x256'; // smallest size for demo
                    }
                    $quality = $param['quality'];
                    $response = FacadesOpenAI::images()->create([
                        'model'           => $model,
                        'prompt'          => $prompt,
                        'size'            => Helper::appIsDemo() ? $demosize : $size,
                        'response_format' => 'b64_json',
                        'quality'         => Helper::appIsDemo() ? 'standard' : $quality,
                        'n'               => 1,
                    ]);
                    $image_url = $response['data'][0]['b64_json'];
                    $contents = base64_decode($image_url);

                    $nameprompt = mb_substr($prompt, 0, 15);
                    $nameprompt = explode(' ', $nameprompt)[0];

                    $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug($nameprompt) . '.png';

                    //save file on local storage or aws s3
                    Storage::disk('public')->put($nameOfImage, $contents);
                    $path = 'uploads/' . $nameOfImage;
                } else {
                    //send prompt to stablediffusion
                    $settings = SettingTwo::first();
                    $stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
                    $stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
                    if ($prompt == null) {
                        return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
                    }
                    if ($stablediffusionKey == '') {
                        return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
                    }
                    $width = intval(explode('x', $image_resolution)[0]);
                    $height = intval(explode('x', $image_resolution)[1]);
                    $client = new Client([
                        'base_uri' => 'https://api.stability.ai/v1/generation/',
                        'headers'  => [
                            'content-type'  => ($stable_type == 'upscale' || $stable_type == 'image-to-image') ? 'multipart/form-data' : 'application/json',
                            'Authorization' => 'Bearer ' . $stablediffusionKey,
                        ],
                    ]);

                    // Stablediffusion engine
                    $engine = $this->settings_two->stablediffusion_default_model;
                    // Content Type
                    $content_type = 'json';

                    $payload = [
                        'cfg_scale'            => 7,
                        'clip_guidance_preset' => $clip_guidance_preset ?? 'NONE',
                        'samples'              => 1,
                        'steps'                => 50,
                    ];

                    if ($sampler) {
                        $payload['sampler'] = $sampler;
                    }

                    if ($style_preset) {
                        $payload['style_preset'] = $style_preset;
                    }

                    switch ($stable_type) {
                        case 'multi-prompt':
                            $stable_url = 'text-to-image';
                            $payload['width'] = $width;
                            $payload['height'] = $height;
                            $arr = [];
                            foreach ($prompt as $p) {
                                $arr[] = [
                                    'text'   => $p . ($mood == null ? '' : (' ' . $mood . ' mood.')),
                                    'weight' => 1,
                                ];
                            }
                            $prompt = $arr;

                            break;
                        case 'upscale':
                            $stable_url = 'image-to-image/upscale';
                            $engine = 'esrgan-v1-x2plus';
                            $payload = [];
                            $payload['image'] = $init_image->get();
                            $prompt = [
                                [
                                    'text'   => $prompt . '-' . Str::random(16),
                                    'weight' => 1,
                                ],
                            ];
                            $content_type = 'multipart';

                            break;
                        case 'image-to-image':
                            $stable_url = $stable_type;
                            $payload['init_image'] = $init_image->get();
                            $prompt = [
                                [
                                    'text'   => $prompt . ($mood == null ? '' : (' ' . $mood . ' mood.')),
                                    'weight' => 1,
                                ],
                            ];
                            $content_type = 'multipart';

                            break;
                        default:
                            $stable_url = $stable_type;
                            $payload['width'] = $width;
                            $payload['height'] = $height;
                            $prompt = [
                                [
                                    'text'   => $prompt . ($mood == null ? '' : (' ' . $mood . ' mood.')),
                                    'weight' => 1,
                                ],
                            ];

                            break;
                    }

                    if ($negative_prompt) {
                        $prompt[] = ['text' => $negative_prompt, 'weight' => -1];
                    }

                    if ($stable_type != 'upscale') {
                        $payload['text_prompts'] = $prompt;
                    }

                    if ($content_type == 'multipart') {
                        $multipart = [];
                        foreach ($payload as $key => $value) {
                            if (! is_array($value)) {
                                $multipart[] = ['name' => $key, 'contents' => $value];

                                continue;
                            }

                            foreach ($value as $multiKey => $multiValue) {
                                $multiName = $key . '[' . $multiKey . ']' . (is_array($multiValue) ? '[' . key($multiValue) . ']' : '') . '';
                                $multipart[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
                            }
                        }
                        $payload = $multipart;
                    }

                    try {
                        $response = $client->post("$engine/$stable_url", [
                            $content_type => $payload,
                        ]);
                    } catch (RequestException $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $statusCode = $response->getStatusCode();
                            // Custom handling for specific status codes here...

                            if ($statusCode == '404') {
                                // Handle a not found error
                            } elseif ($statusCode == '500') {
                                // Handle a server error
                            }

                            $errorMessage = $response->getBody()->getContents();

                            return response()->json(['status' => 'error', 'message' => json_decode($errorMessage)->message]);
                            // Log the error message or handle it as required
                        }

                        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                    } catch (Exception $e) {
                        if ($e->hasResponse()) {
                            $response = $e->getResponse();
                            $statusCode = $response->getStatusCode();
                            // Custom handling for specific status codes here...

                            if ($statusCode == '404') {
                                // Handle a not found error
                            } elseif ($statusCode == '500') {
                                // Handle a server error
                            }

                            $errorMessage = $response->getBody()->getContents();

                            return response()->json(['status' => 'error', 'message' => json_decode($errorMessage)->message]);
                            // Log the error message or handle it as required
                        }

                        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                    }
                    $body = $response->getBody();
                    if ($response->getStatusCode() == 200) {

                        $nameprompt = mb_substr($prompt[0]['text'], 0, 15);
                        $nameprompt = explode(' ', $nameprompt)[0];

                        $nameOfImage = Str::random(12) . '-DALL-E-' . $nameprompt . '.png';

                        $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                    } else {
                        $message = '';
                        if ($body->status == 'error') {
                            $message = $body->message;
                        } else {
                            $message = 'Failed, Try Again';
                        }

                        return response()->json(['status' => 'error', 'message' => $message]);
                    }

                    Storage::disk('public')->put($nameOfImage, $contents);
                    $path = 'uploads/' . $nameOfImage;
                }
                if ($image_storage == self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }
                $entry = new UserOpenai;
                $entry->team_id = $user->team_id;
                $entry->title = request('title') ?: __('New Image');
                $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-workbsook';
                $entry->user_id = Auth::id();
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                if ($image_generator == self::STABLEDIFFUSION) {
                    $entry->input = $prompt[0]['text'];
                } else {
                    $entry->input = $prompt;
                }
                // $entry->input = $prompt[0]['text'];
                $entry->response = $image_generator == 'stablediffusion' ? 'SD' : 'DE';
                $entry->output = $image_storage == self::STORAGE_S3 ? $path : '/' . $path;
                $entry->hash = Str::random(256);
                $entry->credits = 1;
                $entry->words = 0;
                $entry->storage = $image_storage == self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL;
                $entry->payload = request()->all();
                $entry->save();
                $entry->output = ThumbImage($image_storage == self::STORAGE_S3 ? $path : '/' . $path);

                //push each generated image to an array
                array_push($entries, $entry);

                // if in team
                if ($user->getAttribute('team')) {
                    $teamManager = $user->teamManager;
                    if ($teamManager) {
                        if ($teamManager->remaining_images != -1) {
                            $teamManager->remaining_images -= 1;
                            $teamManager->save();
                        }
                        if ($teamManager->remaining_images < -1) {
                            $teamManager->remaining_images = 0;
                            $teamManager->save();
                        }
                    }
                    $member = $user->teamMember;
                    if ($member) {
                        if (! $member->allow_unlimited_credits) {
                            if ($member->remaining_images != -1) {
                                $member->remaining_images -= 1;
                                $member->save();
                            }
                            if ($member->remaining_images < -1) {
                                $member->remaining_images = 0;
                                $member->save();
                            }
                        }
                        $member->used_image_credit += 1;
                        $member->save();
                    }
                } else {
                    if ($user->remaining_images != -1) {
                        $user->remaining_images -= 1;
                        $user->save();
                    }
                    if ($user->remaining_images < -1) {
                        $user->remaining_images = 0;
                        $user->save();
                    }
                }

                Usage::getSingle()->updateImageCounts(1);
            }

            // Release the lock
            Cache::lock($lockKey)->release();

            return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $image_storage]);
        } finally {
            // Always release the lock
            Cache::lock($lockKey)->forceRelease();
        }
    }
}
