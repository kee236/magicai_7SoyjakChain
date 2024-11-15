<?php

namespace App\Services\Assistant;

use App\Helpers\Classes\Helper;
use App\Models\UserOpenaiChat;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;

class AssistantService
{
    public const BASE_URL = 'https://api.openai.com/v1/';

    public const ASSISTANT_URL = 'assistants';

    public const THREAD_URL = 'threads';

    public const MODELS = 'models';

    public const FILES = 'files';

    public const MESSAGE_URL = 'threads/{thread_id}/messages';

    public const RUN_URL = 'threads/{thread_id}/runs';

    public const VECTOR_STORE_FILE = 'vector_stores/{vector_store_id}/files';

    public const VECTOR_STORE_FILE_DELETE = 'vector_stores/{vector_store_id}/files/{file_id}';

    public const VECTOR_STORE = 'vector_stores';

    protected $apiKey;

    protected $client;

    protected $openai;

    public function __construct()
    {
        $this->apiKey = Helper::setOpenAiKey();
        $this->client = new Client;
        $this->openai = OpenAI::factory()
            ->withApiKey($this->apiKey)
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->make();
    }

    /**
     * @throws GuzzleException|Exception
     */
    public function createFile($file)
    {
        $filePath = 'assistant/' . Str::random(20) . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $fullPath = Storage::disk('public')->path($filePath);

        if (! file_exists($fullPath)) {
            throw new Exception('File could not be saved or found: ' . $fullPath);
        }

        try {
            $response = $this->openai->files()->upload([
                'purpose' => 'assistants',
                'file'    => fopen($fullPath, 'rb'),
            ])->toArray();

            return $response;
        } catch (Exception $e) {

            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function createVectorStoreFiles($vectorStoreId, $fileId)
    {
        try {
            $response = $this->client->post(self::BASE_URL . str_replace('{vector_store_id}', $vectorStoreId, self::VECTOR_STORE_FILE), [
                'headers' => $this->getHeaders(),
                'json'    => ['file_id' => $fileId],
            ])->getBody()->getContents();

            return json_decode($response, true);
        } catch (ClientException $e) {
            $errorResponse = $e->getResponse();
            $errorBody = $errorResponse->getBody()->getContents();
            $errorData = json_decode($errorBody, true);

            return [
                'status'  => 'error',
                'message' => $errorData['error']['message'] ?? 'vector upload files error',
            ];
        }

    }

    /**
     * @throws GuzzleException
     */
    public function listVectorStoreFiles($vectorStoreId): array
    {
        $response = $this->client->get(self::BASE_URL . str_replace('{vector_store_id}', $vectorStoreId, self::VECTOR_STORE_FILE), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteVectorStoreFiles($vectorStoreId, $fileId): array
    {
        $url = str_replace(
            ['{vector_store_id}', '{file_id}'],
            [$vectorStoreId, $fileId],
            self::BASE_URL . self::VECTOR_STORE_FILE_DELETE
        );

        $response = $this->client->delete($url, [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function createVectorStore(): array
    {
        $response = $this->client->post(Helper::parseUrl(self::BASE_URL, self::VECTOR_STORE), [
            'headers' => $this->getHeaders(),
            'json'    => ['name' => 'AI Assistant'],
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function showVectorStore($vectorId): array
    {
        $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::VECTOR_STORE_FILE, $vectorId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function listVectorStore(): array
    {
        $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::VECTOR_STORE), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteVectorStore($vectorId): array
    {
        $response = $this->client->delete(Helper::parseUrl(self::BASE_URL, self::VECTOR_STORE, $vectorId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function listFiles(): array
    {
        $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::FILES), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function showFiles($fileId): array
    {
        $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::FILES, $fileId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteFile($fileId): array
    {
        $response = $this->client->delete(Helper::parseUrl(self::BASE_URL, self::FILES, $fileId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function createAssistant($instructions, $assistantName, $model, $resources = null, $tools = null, $description = null, $temperature = null, $topP = null)
    {

        $array = [
            'name'         => $assistantName,
            'instructions' => $instructions,
            'model'        => $model,
        ];

        if (! is_null($description)) {
            $array['description'] = $description;
        }

        if (! is_null($resources)) {
            $array['tool_resources'] = $resources;
        }

        if (! is_null($tools)) {
            $array['tools'] = $tools;
        }

        if (! is_null($temperature)) {
            $array['temperature'] = (float) $temperature;
        }

        if (! is_null($topP)) {
            $array['top_p'] = (float) $topP;
        }

        try {
            $response = $this->client->post(Helper::parseUrl(self::BASE_URL, self::ASSISTANT_URL), [
                'headers' => $this->getHeaders(),
                'json'    => $array,
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            $errorResponse = $e->getResponse();
            $errorBody = $errorResponse->getBody()->getContents();
            $errorData = json_decode($errorBody, true);

            return [
                'status'  => 'error',
                'message' => $errorData['error']['message'] ?? 'api error',
            ];
        }

    }

    /**
     * @throws GuzzleException
     */
    public function updateAssistant($assistantId, $instructions, $assistantName, $model, $resources = null, $tools = null, $description = null, $temperature = null, $topP = null)
    {
        $array = [
            'name'         => $assistantName,
            'instructions' => $instructions,
            'model'        => $model,
        ];

        if (! is_null($description)) {
            $array['description'] = $description;
        }

        if (! is_null($resources)) {
            $array['tool_resources'] = $resources;
        }

        if (! is_null($tools)) {
            $array['tools'] = $tools;
        }

        if (! is_null($temperature)) {
            $array['temperature'] = (float) $temperature;
        }

        if (! is_null($topP)) {
            $array['top_p'] = (float) $topP;
        }

        try {
            $response = $this->client->post(Helper::parseUrl(self::BASE_URL, self::ASSISTANT_URL, $assistantId), [
                'headers' => $this->getHeaders(),
                'json'    => $array,
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            $errorResponse = $e->getResponse();
            $errorBody = $errorResponse->getBody()->getContents();
            $errorData = json_decode($errorBody, true);

            return [
                'status'  => 'error',
                'message' => $errorData['error']['message'] ?? 'api error',
            ];
        }

    }

    /**
     * @throws GuzzleException
     */
    public function listModels(): array|Collection
    {
        try {
            $response = $this->client->get(Helper::parseUrl(self::BASE_URL . self::MODELS), [
                'headers' => $this->getHeaders(),
            ])->getBody()->getContents();

            $models = json_decode($response, true)['data'];

            return collect($models)->filter(function ($model) {
                return str_starts_with($model['id'], 'gpt');
            });

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function listAssistant()
    {
        try {
            $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::ASSISTANT_URL), [
                'headers' => $this->getHeaders(),
            ])->getBody()->getContents();

            return json_decode($response, true)['data'];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'openai api key error',
            ];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function deleteAssistant($assistantId)
    {
        return $this->client->delete(Helper::parseUrl(self::BASE_URL, self::ASSISTANT_URL, $assistantId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();
    }

    /**
     * @throws GuzzleException
     */
    public function showAssistant($assistantId)
    {
        $response = $this->client->get(Helper::parseUrl(self::BASE_URL, self::ASSISTANT_URL, $assistantId), [
            'headers' => $this->getHeaders(),
        ])->getBody()->getContents();

        return json_decode($response, true);
    }

    /**
     * @throws GuzzleException
     */
    public function createThread()
    {
        $response = $this->client->post(Helper::parseUrl(self::BASE_URL, self::THREAD_URL), [
            'headers' => $this->getHeaders(),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function createMessage($threadId, $message)
    {

        if (count($message) > 2 || $message[0]['role'] == 'system') {
            $message = end($message);
        }

        $message['content'] = collect($message['content'])->map(function ($item) {
            if (isset($item['type']) && $item['type'] == 'image_url') {
                if ($item['type'] == 'image_url' && str_starts_with($item['image_url']['url'], '/uploads')) {
                    $item['image_url']['url'] = config('app.url') . $item['image_url']['url'];
                }
            }

            return $item;
        })->toArray();

        if (! isset($message['content'][0]['type'])) {
            $message['content'] = $message['content'][0];

        }

        $response = $this->client->post(self::BASE_URL . str_replace('{thread_id}', $threadId, self::MESSAGE_URL), [
            'headers' => $this->getHeaders(),
            'json'    => $message,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function createRun($chat_bot, $assistantId, $threadId, $main_message)
    {
        $total_used_tokens = 0;
        $output = '';
        $responsedText = '';

        return response()->stream(function () use ($chat_bot, $assistantId, $threadId, $main_message, &$total_used_tokens, &$output, &$responsedText) {
            $chat_id = $main_message->user_openai_chat_id;
            $chat = UserOpenaiChat::whereId($chat_id)->first();

            $stream = $this->client->post(self::BASE_URL . str_replace('{thread_id}', $threadId, self::RUN_URL), [
                'headers' => $this->getHeaders(),
                'json'    => [
                    'assistant_id' => $assistantId,
                    'stream'       => true,
                ],
                'stream' => true,
            ]);
            $data = $stream->getBody()->getContents();
            $events = explode("\n\n", $data);
            foreach ($events as $event) {
                if (str_contains($event, 'thread.message.delta')) {
                    $dataStart = strpos($event, '{');
                    if ($dataStart !== false) {
                        $jsonData = substr($event, $dataStart);
                        $eventData = json_decode($jsonData, true);

                        if (isset($eventData['delta']['content'])) {
                            foreach ($eventData['delta']['content'] as $content) {
                                if ($content['type'] === 'text') {
                                    if (connection_aborted()) {
                                        break 2;
                                    }
                                    $output .= $content['text']['value'];
                                    $responsedText .= $content['text']['value'];
                                    $total_used_tokens += countWords($content['text']['value']);
                                    echo PHP_EOL;
                                    echo "event: data\n";
                                    echo 'data: ' . $content['text']['value'];
                                    echo "\n\n";
                                    flush();
                                }
                            }
                        }
                    }
                }
            }
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

            $main_message->response = $responsedText;
            $main_message->output = $output;
            $main_message->credits = $total_used_tokens;
            $main_message->words = $total_used_tokens;
            $main_message->save();

            $user = Auth::user();
            userCreditDecreaseForWord($user, $total_used_tokens, $chat_bot);
            $chat->total_credits += $total_used_tokens;
            $chat->save();

        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
            'OpenAI-Beta'   => 'assistants=v2',
        ];
    }
}
