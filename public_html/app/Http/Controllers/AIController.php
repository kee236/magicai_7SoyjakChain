<?php

namespace App\Http\Controllers;

use App\Enums\BedrockEngine;
use App\Helpers\Classes\Helper;
use App\Models\Company;
use App\Models\OpenAIGenerator;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use App\Services\Ai\FalAI;
use App\Services\Bedrock\BedrockRuntimeService;
use App\Services\Youtube\YoutubeTranscriptService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use Throwable;

class AIController extends Controller
{
    protected BedrockRuntimeService $bedrockService;

    protected $client;

    protected $settings;

    protected $settings_two;

    public const STABLEDIFFUSION = 'stablediffusion';

    public const FLUXPRO = 'flux-pro';

    public const LOADING_GIF = '/themes/default/assets/img/loading.svg';

    public const STORAGE_S3 = 's3';

    public const CLOUDFLARE_R2 = 'r2';

    public const STORAGE_LOCAL = 'public';

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
        $this->middleware(function (Request $request, $next) {
            Helper::setOpenAiKey();

            return $next($request);
        });
        //Settings
        $this->settings = Setting::first();
        $this->settings_two = SettingTwo::first();
        set_time_limit(120);
    }

    public function buildOutput(Request $request)
    {
        $user = Auth::user();

        if ($request->post_type != 'ai_image_generator' && $user->remaining_words <= 0 && $user->remaining_words != -1) {
            return response()->json(['errors' => 'You have no remaining words. Please upgrade your plan.'], 400);
        }

        $image_generator = $request->image_generator;
        $post_type = $request->post_type;

        //SETTINGS
        $number_of_results = $request->number_of_results ?? 1;
        $maximum_length = $request->maximum_length ?? $this->settings->openai_max_input_length;
        $creativity = $request->creativity ?? $this->settings->openai_default_creativity;

        $language = $request->language;

        try {
            $language = explode('-', $language);
            if (count($language) > 1 && LaravelLocalization::getSupportedLocales()[$language[0]]['name']) {
                $ek = $language[1];
                $language = LaravelLocalization::getSupportedLocales()[$language[0]]['name'];
                $language .= " $ek";
            } else {
                $language = $request->language;
            }
        } catch (Throwable $th) {
            $language = $request->language;
            Log::error($language);
        }

        $negative_prompt = $request->negative_prompt;
        $tone_of_voice = $request->tone_of_voice;
        if ($request->tone_of_voice_custom) {
            $tone_of_voice = $request->tone_of_voice_custom;
        }
        if (! $tone_of_voice) {
            $tone_of_voice = $this->settings->openai_default_tone_of_voice;
        }

        //POST GENERATOR
        if ($post_type == 'post_generator') {
            $description = $request->description;
            $prompt = "Write a post about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different posts. Tone of voice must be $tone_of_voice";
        }

        //POST TITLE GENERATOR
        if ($post_type == 'post_title_generator') {
            $your_description = $request->your_description;
            $prompt = "Post title about $your_description in language $language .Generate $number_of_results post titles. Tone $tone_of_voice.";
        }

        //ARTICLE GENERATOR
        if ($post_type == 'article_generator') {
            $article_title = $request->article_title;
            $focus_keywords = $request->focus_keywords;
            $prompt = "Generate article about $article_title. Focus on $focus_keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice";
        }

        //SUMMARY GENERATOR SUMMARIZER SUMMARIZE TEXT
        if ($post_type == 'summarize_text') {
            $text_to_summary = $request->text_to_summary;
            $tone_of_voice = $request->tone_of_voice;
            if ($request->tone_of_voice_custom) {
                $tone_of_voice = $request->tone_of_voice_custom;
            }

            $prompt = "Summarize the following text: $text_to_summary in $language using a tone of voice that is $tone_of_voice. The summary should be no longer than $maximum_length words and set the creativity to $creativity in terms of creativity. Generate $number_of_results different summaries.";
        }

        //PRODUCT DESCRIPTION
        if ($post_type == 'product_description') {
            $product_name = $request->product_name;
            $description = $request->description;

            $prompt = "Write product description for $product_name. The language is $language. Maximum length is $maximum_length. Creativity is $creativity between 0 to 1. see the following information as a starting point: $description. Generate $number_of_results different product descriptions. Tone $tone_of_voice.";
        }

        //PRODUCT NAME
        if ($post_type == 'product_name') {
            $seed_words = $request->seed_words;
            $product_description = $request->product_description;

            $prompt = "Generate product names that will appeal to customers who are interested in $seed_words. These products should be related to $product_description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different product names. Tone of voice must be $tone_of_voice";
        }

        //TESTIMONIAL REVIEW GENERATOR
        if ($post_type == 'testimonial_review') {
            $subject = $request->subject;
            $prompt = "Generate testimonial for $subject. Include details about how it helped you and what you like best about it. Be honest and specific, and feel free to get creative with your wording Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different testimonials. Tone of voice must be $tone_of_voice";
        }

        //PROBLEM AGITATE SOLUTION
        if ($post_type == 'problem_agitate_solution') {
            $description = $request->description;

            $prompt = "Write Problem-Agitate-Solution copy for the $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. problem-agitate-solution. Tone of voice must be $tone_of_voice Generate $number_of_results different Problem-Afitate-Solution.";
        }

        //BLOG SECTION
        if ($post_type == 'blog_section') {
            $description = $request->description;

            $prompt = " Write me blog section about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog sections. Tone of voice must be $tone_of_voice";
        }

        //BLOG POST IDEAS
        if ($post_type == 'blog_post_ideas') {
            $description = $request->description;

            $prompt = "Write blog post article ideas about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog post ideas. Tone of voice must be $tone_of_voice";
        }

        //BLOG INTROS
        if ($post_type == 'blog_intros') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post intro about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog intros. Tone of voice must be $tone_of_voice";
        }

        //BLOG CONCLUSION
        if ($post_type == 'blog_conclusion') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post conclusion about title: $title. And the description is $description.Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog conclusions. Tone of voice must be $tone_of_voice";
        }

        //FACEBOOK ADS
        if ($post_type == 'facebook_ads') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write facebook ads text about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads text. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO DESCRIPTION
        if ($post_type == 'youtube_video_description') {
            $title = $request->title;

            $prompt = "write youtube video description about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video descriptions. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TITLE
        if ($post_type == 'youtube_video_title') {
            $description = $request->description;

            $prompt = "Craft captivating, attention-grabbing video titles about $description for YouTube rankings. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video titles. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TAG
        if ($post_type == 'youtube_video_tag') {
            $title = $request->title;

            $prompt = "Generate tags and keywords about $title for youtube video. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video tags. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM CAPTIONS
        if ($post_type == 'instagram_captions') {
            $title = $request->title;

            $prompt = "Write instagram post caption about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram captions. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM HASHTAG
        if ($post_type == 'instagram_hashtag') {
            $keywords = $request->keywords;

            $prompt = "Write instagram hastags for $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram hashtags. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST TWEET
        if ($post_type == 'social_media_post_tweet') {
            $title = $request->title;

            $prompt = "Write in 1st person tweet about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tweets. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST BUSINESS
        if ($post_type == 'social_media_post_business') {
            $company_name = $request->company_name;
            $provide = $request->provide;
            $description = $request->description;

            $prompt = "Write in company social media post, company name: $company_name. About: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different social media posts. Tone of voice must be $tone_of_voice";
        }

        //FACEBOOK HEADLINES
        if ($post_type == 'facebook_headlines') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write Facebook ads title about title: $title. And description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads title. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS HEADLINES
        if ($post_type == 'google_ads_headlines') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write Google ads headline product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads headlines. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS DESCRIPTION
        if ($post_type == 'google_ads_description') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write google ads description product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads description. Tone of voice must be $tone_of_voice";
        }

        //CONTENT REWRITE
        if ($post_type == 'content_rewrite') {
            $contents = $request->contents;

            $prompt = "Rewrite content:  '$contents'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different rewrited content. Tone of voice must be $tone_of_voice";
        }

        //PARAGRAPH GENERATOR
        if ($post_type == 'paragraph_generator') {
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice";
        }

        //Pros & Cons
        if ($post_type == 'pros_cons') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Generate pros & cons about title:  '$title'. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different pros&cons. Tone of voice must be $tone_of_voice";
        }

        // META DESCRIPTION
        if ($post_type == 'meta_description') {
            $title = $request->title;
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate website meta description site name: $title. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different meta descriptions. Tone of voice must be $tone_of_voice";
        }

        // FAQ Generator (All datas)
        if ($post_type == 'faq_generator') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Answer like faq about subject: $title Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different faqs. Tone of voice must be $tone_of_voice";
        }

        // Email Generator
        if ($post_type == 'email_generator') {
            $subject = $request->subject;
            $description = $request->description;

            $prompt = "Write email about title: $subject, description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different emails. Tone of voice must be $tone_of_voice";
        }

        // Email Answer Generator
        if ($post_type == 'email_answer_generator') {
            $description = $request->description;

            $prompt = "answer this email content: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different email answers. Tone of voice must be $tone_of_voice";
        }

        // Newsletter Generator
        if ($post_type == 'newsletter_generator') {
            $description = $request->description;
            $subject = $request->subject;
            $title = $request->title;

            $prompt = "generate newsletter template about product_title: $title, reason: $subject description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different newsletter template. Tone of voice must be $tone_of_voice";
        }

        // Grammar Correction
        if ($post_type == 'grammar_correction') {
            $description = $request->description;

            $prompt = "Correct this to standard $language. Text is '$description'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different grammar correction. Tone of voice must be $tone_of_voice";
        }

        // TL;DR summarization
        if ($post_type == 'tldr_summarization') {
            $description = $request->description;

            $prompt = "$description. Tl;dr Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tl;dr. Tone of voice must be $tone_of_voice";
        }

        if ($post_type == 'ai_rewriter') {
            $content_rewrite = $request->content_rewrite;
            $rewrite_mode = $request->rewrite_mode;

            $prompt = "Original Content: $content_rewrite.\n\n\nMust Rewrite content with $rewrite_mode mode differently with original content. Result language is $language \n";
        }

        if ($post_type == 'ai_image_generator') {
            $imageParam = $request->all();
            // $description = $request->description;
            // $prompt = "$description";
            // $size = $request->size;
            // $style = $request->image_style;
            // $lighting = $request->image_lighting;
            // $mood = $request->image_mood;
            // $number_of_images = (int)$request->image_number_of_images;
        }

        if ($post_type == 'ai_video') {
            $videoParam = $request->all();
        }

        if ($post_type == 'ai_code_generator') {
            $description = $request->description;
            $code_language = $request->code_language;
            $prompt = "Write a code about $description, in $code_language";
        }

        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if ($post->custom_template == 1) {
            $custom_template = OpenAIGenerator::find($request->openai_id);
            $prompt = $custom_template->prompt;
            foreach (json_decode($custom_template->questions) as $question) {
                $question_name = '**' . $question->name . '**';
                $prompt = str_replace($question_name, $request[$question->name], $prompt);
            }

            $prompt .= " in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters.";
            if ($creativity !== 'undefined') {
                $prompt .= " Creativity is $creativity between 0 and 1.";
            }
            if ($tone_of_voice !== 'undefined') {
                $prompt .= " Tone of voice must be $tone_of_voice.";
            }
        }

        if ($post->type == 'youtube') {
            $language = $request->language;
            $youtube_action = $request->youtube_action;
            if ($youtube_action == 'blog') {
                $prompt = "You are blog writer. Turn the given transcript text into a blog post in and translate to {$language} language. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html). Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'short') {
                $prompt = "You are transcript editor. Make sense of the given content and explain the main idea. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'list') {
                $prompt = "You are transcript editor. Make sense of the given content and make a list main ideas. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'tldr') {
                $prompt = "You are transcript editor. Make short TLDR. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action == 'prons_cons') {
                $prompt = "You are transcript editor. Make short pros and cons. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            }

            $videoUrl = $request->url;
            $data = (new youtubeTranscriptService)
                ->getTranscript($videoUrl);
            $transcripts = json_decode($data->content(), true, 512, JSON_THROW_ON_ERROR);
            if (isset($transcripts['captions'])) {
                $prompt .= $transcripts['captions'] . "\n";
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('There are no captions available in the video.'),
                ], 401);
            }
        }

        if ($post->type == 'rss') {
            $language = $request->language;
            $prompt = "write blog post about {$request->title}. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html).";
            $prompt .= "Your result must be in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters. Tone of voice must be $tone_of_voice. Creativity is $creativity between 0 and 1.";
        }

        // check if there is a company input included in the request
        if ($request->company) {
            $company = Company::find($request->company);
            $product = Product::find($request->product);
            if ($company) {
                if (! isset($prompt)) {
                    $prompt = '';
                }
                $type = $product->type == 0 ? 'Service' : 'Product';
                $prompt .= ".\n Focus on my company and {$type}'s information: \n";
                // Company information
                if ($company->name) {
                    $prompt .= "The company's name is {$company->name}. ";
                }
                // explode industry
                $industry = explode(',', $company->industry);
                $count = count($industry);
                if ($count > 0) {
                    $prompt .= 'The company is in the ';
                    foreach ($industry as $index => $ind) {
                        $prompt .= $ind;
                        if ($index < $count - 1) {
                            $prompt .= ' and ';
                        }
                    }
                }

                if ($company->website) {
                    $prompt .= ". The company's website is {$company->website}. ";
                }

                if ($company->target_audience) {
                    $prompt .= "The company's target audience is: {$company->target_audience}. ";
                }

                if ($company->tagline) {
                    $prompt .= "The company's tagline is {$company->tagline}. ";
                }

                if ($company->description) {
                    $prompt .= "The company's description is {$company->description}. ";
                }
                if ($product) {
                    // Product information
                    if ($product->key_features) {
                        $prompt .= "The {$product->type}'s key features are {$product->key_features}. ";
                    }

                    if ($product->name) {
                        $prompt .= "The {$product->type}'s name is {$product->name}. \n";
                    }
                }
            }
        }

        if ($post->type == 'text' || $post->type == 'rss' || $post->type == 'youtube') {
            return $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user);
        }

        if ($post->type == 'code') {
            return $this->codeOutput($prompt, $post, $user);
        }

        if ($post->type == 'image') {
            return $this->imageOutput($imageParam, $post, $user);
        }

        if ($post->type == 'video') {
            return $this->videoOutput($videoParam, $post, $user);
        }

        if ($post->type == 'audio') {
            $file = $request->file('file');

            return $this->audioOutput($file, $post, $user);
        }

        if ($post->type == 'isolator') {
            $request->validate([
                'file' => 'required|file|mimes:ogg,mpga,mp3,mp4,mpeg,m4a,wav,webm',
            ], [
                'file.mimes' => __('Invalid file extension, accepted extensions are ogg, mp3, mp4, mpeg, mpga, m4a, wav, and webm.'),
                'file.max'   => __('The file size must not exceed 500 MB.'),
            ]);
            $file = $request->file('file');

            return $this->audioIsolator($file, $post, $user);
        }
    }

    public function streamedTextOutput(Request $request)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;
        $message_id = $request->message_id;
        $message = UserOpenai::whereId($message_id)->first();
        $prompt = $message->input;

        $youtube_url = $request->youtube_url;
        $rss_image = $request->rss_image;

        $creativity = $request->creativity;
        $maximum_length = $request->maximum_length;
        $number_of_results = $request->number_of_results;

        return response()->stream(function () use ($prompt, $message_id, $settings, $creativity, $maximum_length, $number_of_results, $youtube_url, $rss_image) {

            try {
                if ($settings->openai_default_model == 'text-davinci-003') {
                    $stream = FacadesOpenAI::completions()->createStreamed([
                        'model'       => $settings->openai_default_model,
                        'prompt'      => $prompt,
                        'temperature' => (int) $creativity,
                        'max_tokens'  => (int) $maximum_length,
                        'n'           => (int) $number_of_results,
                    ]);
                } else {
                    if ((int) $number_of_results > 1) {
                        $prompt = $prompt . ' number of results should be ' . (int) $number_of_results;
                    }
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model'    => $settings->openai_default_model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                    ]);
                }
            } catch (Exception $exception) {
                $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                echo "data: $messageError";
                echo "\n\n";
                //ob_flush();
                flush();
                echo 'data: [DONE]';
                echo "\n\n";
                //ob_flush();
                flush();
                usleep(50000);
            }

            $total_used_tokens = 0;
            $output = '';
            $responsedText = '';

            // Youtube Thumbnail
            if ($youtube_url) {
                $parsedUrl = parse_url($youtube_url);
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $queryParameters);
                    if (isset($queryParameters['v'])) {
                        $video_id = $queryParameters['v'];
                    }
                }
                $video_thumbnail = sprintf('https://img.youtube.com/vi/%s/maxresdefault.jpg', $video_id);

                $contents = file_get_contents($video_thumbnail);
                $nameOfImage = "youtube-$video_id.jpg";

                //save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;
                $uploadedFile = new File(substr($path, 1));

                if (SettingTwo::first()->ai_image_storage == 's3') {
                    try {
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";

                $total_used_tokens += 1;
                $needChars = 6000 - 1;
                $random_text = Str::random($needChars);
                echo 'data: ' . $output . '/**' . $random_text . "\n\n";
                //ob_flush();
                flush();
                usleep(500);
            }

            // RSS Thumbnail
            if ($rss_image) {

                $contents = file_get_contents($rss_image);
                $nameOfImage = 'rss-' . Str::random(12) . '.jpg';

                //save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;
                $uploadedFile = new File(substr($path, 1));

                if (SettingTwo::first()->ai_image_storage == 's3') {
                    try {
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";

                $total_used_tokens += 1;
                $needChars = 6000 - 1;
                $random_text = Str::random($needChars);
                echo 'data: ' . $output . '/**' . $random_text . "\n\n";
                //ob_flush();
                flush();
                usleep(500);
            }

            foreach ($stream as $response) {
                if ($settings->openai_default_model == 'text-davinci-003') {
                    if (isset($response->choices[0]->text)) {
                        $message = $response->choices[0]->text;
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);
                        echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                        //ob_flush();
                        flush();
                        usleep(500);
                    }
                } else {
                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);

                        echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                        //ob_flush();
                        flush();
                        usleep(500);
                    }
                }

                if (connection_aborted()) {
                    break;
                }
            }

            $message = UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $message->save();

            $user = Auth::user();

            userCreditDecreaseForWord($user, $total_used_tokens, $settings->openai_default_model);

            echo 'data: [DONE]';
            echo "\n\n";
            //ob_flush();
            flush();
            usleep(50000);
        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);
    }

    public function textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user)
    {
        $user = Auth::user();

        if ($user->remaining_words <= 0 and $user->remaining_words != -1) {
            $data = [
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            ];

            return response()->json($data, 419);
        }
        $entry = new UserOpenai;
        $entry->team_id = $user->team_id;
        $entry->title = request('title') ?: __('New Workbook');
        $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = null;
        $entry->output = null;
        $entry->hash = str()->random(256);
        $entry->credits = 0;
        $entry->words = 0;
        $entry->save();

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();

        return response()->json(compact('message_id', 'html', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt'));
    }

    public function codeOutput($prompt, $post, $user)
    {
        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $response = FacadesOpenAI::completions()->create([
                'model'      => $this->settings->openai_default_model,
                'prompt'     => $prompt,
                'max_tokens' => (int) $this->settings->openai_max_output_length,
            ]);
        } else {
            $response = FacadesOpenAI::chat()->create([
                'model'    => $this->settings->openai_default_model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
        }

        $total_used_tokens = $response->usage->totalTokens;

        $entry = new UserOpenai;
        $entry->team_id = $user->team_id;
        $entry->title = request('title') ?: __('New Workbook');
        $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = json_encode($response->toArray());

        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $entry->output = $response['choices'][0]['text'];
            $total_used_tokens = countWords($entry->output);
        } else {
            $entry->output = $response->choices[0]->message->content;
            $total_used_tokens = countWords($entry->output);
        }

        $entry->hash = Str::random(256);
        $entry->credits = $total_used_tokens;
        $entry->words = 0;
        $entry->save();

        $user = Auth::user();

        userCreditDecreaseForWord($user, $total_used_tokens, $this->settings->openai_default_model);

        return $this->finalizeOutput($post, $entry);
    }

    public function chatImageOutput(Request $request)
    {
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

        $prompt = $request->input('prompt');
        $history = $request->input('chatHistory');

        if ($this->settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $this->settings?->openai_api_secret);
        }
        $openaiKey = $apiKeys[array_rand($apiKeys)];

        $client = OpenAI::factory()
            ->withApiKey($openaiKey)
            ->withHttpClient(new \GuzzleHttp\Client)
            ->make();

        $completion = $client->chat()->create([
            'model'    => 'gpt-3.5-turbo',
            'messages' => [[
                'role'    => 'user',
                'content' => "Write what does user want to draw at the last moment of chat history. \n\n\nChat History: $history \n\n\n\n Result is 'Draw an image of ... ",
            ]],
        ]);

        $path = '';
        $settings = Setting::first();
        // Fetch the Site Settings object with openai_api_secret
        if ($this->settings?->user_api_option) {
            $apiKeys = explode(',', auth()->user()?->api_keys);
        } else {
            $apiKeys = explode(',', $this->settings?->openai_api_secret);
        }
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
        set_time_limit(120);

        $nameOfImage = Str::random(12) . '.png';
        $response = FacadesOpenAI::images()->create([
            'model'           => 'dall-e-3',
            'prompt'          => $completion->choices[0]->message->content,
            'size'            => '1024x1024',
            'response_format' => 'b64_json',
        ]);
        $image_url = $response['data'][0]['b64_json'];
        $contents = base64_decode($image_url);

        //save file on local storage or aws s3
        Storage::disk('public')->put($nameOfImage, $contents);
        $path = '/uploads/' . $nameOfImage;
        $uploadedFile = new File(substr($path, 1));

        if (SettingTwo::first()->ai_image_storage == 's3') {
            try {
                $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                unlink(substr($path, 1));
                $path = Storage::disk('s3')->url($aws_path);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
            }
        }

        userCreditDecreaseForImage($user, 1, 'dall-e-3');

        return response()->json(['path' => $path]);
    }

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

            if (! in_array($image_generator, [self::STABLEDIFFUSION, self::FLUXPRO])) {
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
                $image_model = '';
                if (! in_array($image_generator, [self::STABLEDIFFUSION, self::FLUXPRO])) {
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
                    $image_model = $model;
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

                    if ($image_storage == self::CLOUDFLARE_R2) {
                        Storage::disk('r2')->put($nameOfImage, $contents);
                        $path = Storage::disk('r2')->url($nameOfImage);
                    } else {
                        //save file on local storage or aws s3
                        Storage::disk('public')->put($nameOfImage, $contents);
                        $path = 'uploads/' . $nameOfImage;
                    }
                } else {
                    $v2BetaEngines = ['sd3', 'sd3-turbo', 'sd3-medium', 'sd3-large', 'sd3-large-turbo', 'core', 'ultra'];
                    //send prompt to stablediffusion
                    $settings = SettingTwo::first();
                    $width = intval(explode('x', $image_resolution)[0]);
                    $height = intval(explode('x', $image_resolution)[1]);

                    if ($settings->stablediffusion_default_model === BedrockEngine::BEDROCK->value && $stable_type == 'text-to-image') {
                        $response = $this->bedrockService->invokeStableDiffusion(
                            $prompt, random_int(1, 1000000), $width, $height);

                        $entry = new UserOpenai;
                        $entry->team_id = $user->team_id;
                        $entry->title = request('title') ?: __('New Image');
                        $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-workbsook';
                        $entry->user_id = Auth::id();
                        $entry->openai_id = $post->id;
                        $entry->input = $prompt;
                        $entry->response = 'SD';
                        $entry->output = url($response);
                        $entry->hash = Str::random(256);
                        $entry->credits = 1;
                        $entry->words = 0;
                        $entry->storage = $image_storage == self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL;
                        $entry->payload = request()->all();
                        $entry->save();
                        $entry->output = ThumbImage($response);

                        //push each generated image to an array
                        array_push($entries, $entry);
                        userCreditDecreaseForImage($user, 1);

                        Cache::lock($lockKey)->release();

                        return response()->json(['status' => 'success', 'images' => $entries, 'image_storage' => $image_storage]);
                    } elseif ($image_generator == self::STABLEDIFFUSION) {
                        $stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
                        $stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
                        if ($prompt == null) {
                            return response()->json(['status' => 'error', 'message' => 'You must provide a prompt']);
                        }
                        if ($stablediffusionKey == '') {
                            return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
                        }

                        // Stablediffusion engine
                        $engine = $this->settings_two->stablediffusion_default_model;
                        $image_model = $engine;
                        $sd3Payload = [];

                        if (
                            in_array($engine, $v2BetaEngines)
                            &&
                            ($stable_type == 'text-to-image' || $stable_type == 'image-to-image')
                        ) {
                            $client = new Client([
                                'base_uri' => 'https://api.stability.ai/v2beta/stable-image/generate/',
                                'headers'  => [
                                    'content-type'  => 'multipart/form-data',
                                    'Authorization' => 'Bearer ' . $stablediffusionKey,
                                    'accept'        => 'application/json',
                                ],
                            ]);

                        } else {
                            $client = new Client([
                                'base_uri' => 'https://api.stability.ai/v1/generation/',
                                'headers'  => [
                                    'content-type'  => ($stable_type == 'upscale' || $stable_type == 'image-to-image') ? 'multipart/form-data' : 'application/json',
                                    'Authorization' => 'Bearer ' . $stablediffusionKey,
                                    'accept'        => 'application/json',
                                ],
                            ]);
                        }

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

                                $sd3Payload = [
                                    [
                                        'name'     => 'prompt',
                                        'contents' => $prompt,
                                    ],
                                    [
                                        'name'     => 'mode',
                                        'contents' => 'image-to-image',
                                    ],
                                    [
                                        'name'     => 'strength',
                                        'contents' => 0,
                                    ],
                                    [
                                        'name'     => 'image',
                                        'contents' => $init_image->get(),
                                        'filename' => $init_image->getClientOriginalName(),
                                    ],
                                ];
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
                                $sd3Payload = [
                                    [
                                        'name'     => 'prompt',
                                        'contents' => $prompt,
                                    ],
                                    [
                                        'name'     => 'file',
                                        'contents' => 'no',
                                    ],
                                    [
                                        'name'     => 'output_format',
                                        'contents' => 'png',
                                    ],
                                ];
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
                            if (
                                in_array($engine, $v2BetaEngines) &&
                                ($stable_type == 'text-to-image' || $stable_type == 'image-to-image')
                            ) {
                                $validEngines = ['sd3-turbo', 'sd3-medium', 'sd3-large', 'sd3-large-turbo'];
                                if (in_array($engine, $validEngines)) {
                                    $engine = 'sd3';
                                    $sd3Payload[] = [
                                        'name'     => 'model',
                                        'contents' => $engine,
                                    ];
                                }

                                $response = $client->post("$engine", [
                                    'headers' => [
                                        'accept' => 'application/json',
                                    ],
                                    'multipart' => $sd3Payload,
                                ]);
                            } else {

                                if ($stable_type == 'multi-prompt') {
                                    $engine = 'stable-diffusion-v1-6';
                                }

                                $response = $client->post("$engine/$stable_url", [
                                    $content_type => $payload,
                                ]);
                            }

                        } catch (Exception $e) {

                            if ($e->hasResponse()) {
                                $response = $e->getResponse();
                                $statusCode = $response->getStatusCode();
                                // Custom handling for specific status codes here...

                                if ($statusCode == 403) {
                                    // Handle content moderation error
                                    $errorMessage = $response->getBody()->getContents();
                                    $errorData = json_decode($errorMessage, true);

                                    return response()->json([
                                        'status'  => 'error',
                                        'message' => $errorData['errors'],
                                        'name'    => $errorData['name'],
                                    ], 403);
                                }
                                if ($statusCode == '404') {
                                    // Handle a not found error
                                } elseif ($statusCode == '500') {
                                    // Handle a server error
                                }
                                $errorMessage = $response->getBody()->getContents();
                                $errorData = json_decode($errorMessage, true);

                                return response()->json(['status' => 'error', 'message' => $errorData['message']]);
                            }

                            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                        }

                        $body = $response->getBody();

                        if ($response->getStatusCode() == 200) {
                            $nameprompt = mb_substr($prompt[0]['text'], 0, 15);
                            $nameprompt = explode(' ', $nameprompt)[0];

                            $nameOfImage = Str::random(12) . '-DALL-E-' . $nameprompt . '.png';

                            if (
                                in_array($engine, $v2BetaEngines) &&
                                ($stable_type == 'text-to-image' || $stable_type == 'image-to-image')
                            ) {
                                $contents = base64_decode(json_decode($body)->image);
                            } else {
                                $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                            }
                        } else {
                            $message = '';
                            if ($body->status == 'error') {
                                $message = $body->message;
                            } else {
                                $message = 'Failed, Try Again';
                            }

                            return response()->json(['status' => 'error', 'message' => $message]);
                        }

                        if ($image_storage == self::CLOUDFLARE_R2) {
                            Storage::disk('r2')->put($nameOfImage, $contents);
                            $path = Storage::disk('r2')->url($nameOfImage);
                        } else {
                            //save file on local storage or aws s3
                            Storage::disk('public')->put($nameOfImage, $contents);
                            $path = 'uploads/' . $nameOfImage;
                        }
                    } else {
                        $request_id = FalAI::generate(request('description_flux_pro'));
                    }
                }

                if (! isset($request_id)) {
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
                }

                $filePath = null;

                if (! isset($request_id) && isset($path)) {
                    if ($image_storage == self::STORAGE_S3 || $image_storage == self::CLOUDFLARE_R2) {
                        $filePath = $path;
                    } elseif ($image_storage == self::STORAGE_LOCAL) {
                        $filePath = '/' . $path;
                    }
                }

                //last
                $entry = new UserOpenai;
                $entry->team_id = $user->team_id;
                $entry->title = request('title') ?: __('New Image');
                $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-workbsook';
                $entry->user_id = Auth::id();
                $entry->openai_id = $post->id;
                $entry->input = $prompt ?: request('description_flux_pro');

                if (! isset($request_id)) {
                    if (in_array($image_generator, [self::STABLEDIFFUSION])) {
                        $entry->input = $prompt[0]['text'] ?: \request('description_flux_pro');
                    } else {
                        $entry->input = $prompt;
                    }
                } else {
                    $entry->input = request('description_flux_pro');
                }

                // $entry->input = $prompt[0]['text'];
                $entry->response = $image_generator == 'stablediffusion' ? 'SD' : 'DE';

                if (isset($request_id)) {
                    $entry->response = 'FL';
                }

                if ($image_generator == self::FLUXPRO) {
                    $entry->output = ThumbImage(asset(self::LOADING_GIF));
                } else {
                    $entry->output = url($filePath);
                }

                if (isset($request_id)) {
                    $entry->request_id = $request_id;
                    $entry->status = 'IN_QUEUE';
                }

                $entry->hash = Str::random(256);
                $entry->credits = 1;
                $entry->words = 0;
                $entry->storage = $image_storage == self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL;
                $entry->payload = request()->all();

                $entry->save();

                if ($image_generator == self::FLUXPRO) {
                    $entry->output = ThumbImage(asset(self::LOADING_GIF));
                } else {
                    $entry->output = ThumbImage($filePath);
                }

                $entry->img_id = 'img-' . $entry->response . '-' . $entry->id;
                //push each generated image to an array
                array_push($entries, $entry);
                userCreditDecreaseForImage($user, 1);
            }

            // Release the lock
            Cache::lock($lockKey)->release();

            return response()->json([
                'status'        => 'success',
                'images'        => $entries,
                'image_storage' => $image_storage,
            ]);
        } finally {
            // Always release the lock
            Cache::lock($lockKey)->forceRelease();
        }
    }

    public function videoOutput($param, $post, $user)
    {
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

        set_time_limit(120);

        $init_image = file_get_contents($param['image_src']);
        Log::info($init_image);

        $nameOfImage = Str::random(12) . '.png';
        Log::info($nameOfImage);
        Storage::disk('public')->put($nameOfImage, $init_image);
        $path = '/uploads/' . $nameOfImage;
        $uploadedFile = new File(substr($path, 1));

        if (SettingTwo::first()->ai_image_storage == 's3') {
            try {
                $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                unlink(substr($path, 1));
                $path = Storage::disk('s3')->url($aws_path);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
            }
        }

        $seed = $param['seed'];
        $cfg_scale = $param['cfg_scale'];
        $motion_bucket_id = $param['motion_bucket_id'];

        $image_storage = $this->settings_two->ai_image_storage;

        //send prompt to stablediffusion
        $settings = SettingTwo::first();
        $stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
        $stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
        if ($stablediffusionKey == '') {
            return response()->json(['status' => 'error', 'message' => 'You must provide a StableDiffusion API Key.']);
        }

        $client = new Client([
            'base_uri' => 'https://api.stability.ai/v2beta/',
            'headers'  => [
                'content-type'  => 'multipart/form-data',
                'Authorization' => 'Bearer ' . $stablediffusionKey,
            ],
        ]);

        $payload = [
            'image'            => $init_image,
            'cfg_scale'        => $cfg_scale,
            'seed'             => $seed,
            'motion_bucket_id' => $motion_bucket_id,
        ];

        $multipart = [];
        foreach ($payload as $key => $value) {
            if ($key == 'image') {
                $multipart[] = ['name' => $key, 'contents' => $value, 'filename' => 'image.png'];
            } else {
                $multipart[] = ['name' => $key, 'contents' => $value];
            }
        }
        $payload = $multipart;

        try {
            $response = $client->post('image-to-video', [
                'multipart' => $payload,
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

                return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
                // Log the error message or handle it as required
            }

            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        $body = $response->getBody();
        if ($response->getStatusCode() == 200) {
            return response()->json(['status' => 'success', 'id' => json_decode($body)->id, 'sourceUrl' => $path]);
        } else {
            $message = '';
            if ($body->status == 'error') {
                $message = $body->message;
            } else {
                $message = 'Failed, Try Again';
            }

            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }

    public function checkVideoProgress(Request $request)
    {
        $resultId = $request->id;
        $user = Auth::user();

        $client = new Client;
        $settings = SettingTwo::first();
        $stableDiffusionKeys = explode(',', $settings->stable_diffusion_api_key);
        $stableDiffusionKey = $stableDiffusionKeys[array_rand($stableDiffusionKeys)];

        $client = new Client([
            'base_uri' => 'https://api.stability.ai/v2alpha/generation/image-to-video/result/' . $resultId,
            'headers'  => [
                'Accept'        => 'video/*',
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
            ],
        ]);

        try {
            $response = $client->request('GET');
            if ($response->getStatusCode() === 200) {
                $fileContents = $response->getBody()->getContents();
                $nameOfImage = 'image-to-video-' . Str::random(12) . '.mp4';
                Storage::disk('public')->put($nameOfImage, $fileContents);
                $path = 'uploads/' . $nameOfImage;

                $imageStorage = $this->settings_two->ai_image_storage;
                if ($imageStorage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $entry = UserOpenai::create([
                    'team_id'   => $user->team_id,
                    'title'     => __('New Video'),
                    'slug'      => Str::random(7) . Str::slug($user->fullName()) . '-workbsook',
                    'user_id'   => Auth::id(),
                    'openai_id' => OpenAIGenerator::where('slug', 'ai_video')->first()->id,
                    'input'     => $request->url,
                    'response'  => 'VIDEO',
                    'output'    => $imageStorage === self::STORAGE_S3 ? $path : '/' . $path,
                    'hash'      => Str::random(256),
                    'credits'   => 5,
                    'words'     => 0,
                    'storage'   => $imageStorage === self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL,
                    'payload'   => request()->all(),
                ]);

                userCreditDecreaseForImage($user, 5, 'image-to-video');

                return response()->json(['status' => 'success', 'status' => 'finished', 'url' => $path, 'video' => $entry]);
            }

            if ($response->getStatusCode() === 202) {
                return response()->json(['status' => 'success', 'status' => 'in-progress']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * @throws Throwable
     * @throws JsonException
     */
    public function audioOutput($file, $post, $user)
    {

        $path = 'upload/audio/';

        $file_name = Str::random(4) . '-' . Str::slug($user->fullName()) . '-audio.' . $file->getClientOriginalExtension();

        //Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        $file->move($path, $file_name);
        $fullPath = $path . $file_name;

        $response = FacadesOpenAI::audio()->transcribe([
            'file'            => fopen($fullPath, 'r'),
            'model'           => 'whisper-1',
            'response_format' => 'verbose_json',
        ]);

        $text = $response->text;

        $entry = UserOpenai::create([
            'team_id'   => $user->team_id,
            'title'     => request('title') ?: __('New Workbook'),
            'slug'      => Str::random(7) . Str::slug($user->fullName()) . '-speech-to-text-workbook',
            'user_id'   => Auth::id(),
            'openai_id' => $post->id,
            'input'     => $fullPath,
            'response'  => json_encode($response->toArray(), JSON_THROW_ON_ERROR),
            'output'    => $text,
            'hash'      => Str::random(256),
            'credits'   => countWords($text),
            'words'     => countWords($text),
        ]);

        $team = $user->getAttribute('team');

        userCreditDecreaseForWord($user, countWords($text), 'whisper-1');

        //Workbook add-on
        $workbook = $entry;

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html2'));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Throwable
     * @throws JsonException
     */
    public function audioIsolator($file, $post, $user)
    {
        set_time_limit(3000);
        $apiKey = $this->settings_two->elevenlabs_api_key;
        $mp3File = $file->getRealPath();
        $mp3FileName = $file->getClientOriginalName();
        $client = new Client;
        $response = $client->request('POST', 'https://api.elevenlabs.io/v1/audio-isolation', [
            'headers' => [
                'xi-api-key' => $apiKey,
            ],
            'multipart' => [
                [
                    'name'     => 'audio',
                    'contents' => fopen($mp3File, 'r'),
                    'filename' => $mp3FileName,
                ],
            ],
            'timeout' => 3000,
        ]);
        $resAudio = $response->getBody();
        $characterCost = $response->getHeader('character-cost');
        if (count($characterCost) > 0) {
            $characterCost = $characterCost[0];
        } else {
            $characterCost = 0;
        }
        $defaultWordChars = 5;
        $wordsCount = $characterCost / $defaultWordChars;

        userCreditDecreaseForWord($user, $wordsCount, 'isolator');

        $audioName = $user->id . '-' . Str::random(20) . '.mp3';
        Storage::disk('public')->put($audioName, $resAudio);
        $ai = OpenAIGenerator::whereSlug('ai_voice_isolator')->first();

        $langsAndVoices['language'][] = 'en-US';
        $langsAndVoices['voices'][] = $audioName;

        $entry = UserOpenai::create([
            'team_id'   => $user->team_id,
            'output'    => $audioName,
            'input'     => $audioName,
            'title'     => __('Isolated Voice'),
            'slug'      => Str::random(20) . Str::slug($user->fullName()) . '-isolated-voice',
            'user_id'   => $user->id,
            'hash'      => Str::random(256),
            'openai_id' => $ai->id,
        ]);

        $entry->response = json_encode($langsAndVoices, JSON_THROW_ON_ERROR);

        $entry->credits = $wordsCount;
        $entry->words = $wordsCount;
        $entry->save();

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $ai->id)->orderBy('created_at', 'desc')->paginate(10);
        $userOpenai->withPath(route('dashboard.user.openai.generator', 'ai_voice_isolator'));
        $openai = OpenAIGenerator::where('id', $ai->id)->first();
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html2'));
    }

    public function finalizeOutput($post, $entry)
    {
        //Workbook add-on
        $workbook = $entry;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);

        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html', 'html2'));
    }

    public function messageTitleSave(Request $request)
    {
        if (! $request['message_id'] || ! $request['title']) {
            return response()->json([
                'message' => trans('TItle is required'),
            ]);
        }

        $entry = UserOpenai::find($request->message_id);
        $entry->title = request('title');
        $entry->save();

        return response()->json([
            'message' => trans('Title updated'),
        ]);
    }

    public function lowGenerateSave(Request $request)
    {
        $user = Auth::user();
        $response = $request->response;
        $total_user_tokens = countWords($response);

        $entry = UserOpenai::find($request->message_id);
        if ($entry == null) {
            $entry = new UserOpenai;
            $entry->user_id = $user->id;
            $entry->input = $response;
            $entry->hash = str()->random(256);
            $entry->team_id = $user->team_id;
            $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
            $entry->openai_id = $request->openai_id ?? 1;
        }
        $entry->title = request('title') ?: __('New Workbook');
        $entry->credits = $total_user_tokens;
        $entry->words = $total_user_tokens;
        $entry->response = $response;
        $entry->output = $response;
        $entry->save();

        $reSave = $request->boolean('resave', false);
        if (! $reSave) {
            userCreditDecreaseForWord($user, $total_user_tokens, $this->settings->openai_default_model);
        }
    }

    public function lazyLoadImage(Request $request)
    {
        $items_per_page = 5;
        $offset = $request->get('offset', 0);
        $post_type = $request->get('post_type');
        $post = OpenAIGenerator::where('slug', $post_type)->first();

        $all_images = UserOpenai::where('user_id', Auth::id())
            ->where('openai_id', $post->id);

        $all_images_count = $all_images->count();
        $current_images_list = $all_images->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($items_per_page)
            ->get();
        $thumbnails = [];
        foreach ($current_images_list as $image) {
            // Generate thumbnail URL using your existing method
            $thumbnailUrl = ThumbImage($image->output);
            // Append the image object with thumbnail URL to the array
            $imageWithThumbnail = $image->toArray(); // Convert the image object to an array
            $imageWithThumbnail['thumbnail'] = $thumbnailUrl; // Add thumbnail URL to the array
            $thumbnails[] = $imageWithThumbnail; // Append the modified array to the thumbnails array
        }

        return response()->json([
            'images'          => $thumbnails,
            'count_current'   => $current_images_list->count() + $offset,
            'count_remaining' => $all_images_count - ($current_images_list->count() + $offset),
            'count_all'       => $all_images_count,
        ]);
    }

    public function updateWriting(Request $request)
    {
        $user = $request->user();

        $content = $request->get('content');
        $prompt = $request->prompt;

        if ($content == null || $content == '') {
            return response()->json(['result' => '']);
        }

        if ($user->remaining_words <= 0 && $user->remaining_words != -1) {
            return response()->json(['errors' => 'You have no remaining words. Please upgrade your plan.'], 400);
        }

        Helper::setOpenAiKey();
        $chat_bot = $this->settings?->openai_default_model ?? 'gpt-3.5-turbo';
        $detect = FacadesOpenAI::chat()->create([
            'model'    => $chat_bot,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful language detection assistant. Must detect the content language and return it only ' . app()->getLocale() . ' language code. For example: "en_US"',
                ],
                [
                    'role'    => 'user',
                    'content' => "$prompt\n\nContent: \"$content\"",
                ],
            ],
        ]);

        $languageCode = $detect->choices[0]->message->content;

        $completion = FacadesOpenAI::chat()->create([
            'model'    => $chat_bot,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful assistant. You must response only with answer. Response language must be: ' . $languageCode,
                ],
                [
                    'role'    => 'user',
                    'content' => "$prompt\n\nContent: \"$content\"",
                ],
            ],
        ]);

        $content = $completion->choices[0]->message->content;

        userCreditDecreaseForWord($user, countWords($content), $chat_bot);

        return response()->json(['result' => $completion->choices[0]->message->content]);
    }

    public function reWrite()
    {
        $openai = OpenAIGenerator::whereSlug('ai_rewriter')->firstOrFail();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        if ($this->settings_two->openai_default_stream_server == 'backend') {
            $apikeyPart1 = base64_encode(rand(1, 100));
            $apikeyPart2 = base64_encode(rand(1, 100));
            $apikeyPart3 = base64_encode(rand(1, 100));
        } else {
            $settings = Setting::first();
            // Fetch the Site Settings object with openai_api_secret
            if ($this->settings?->user_api_option) {
                $apiKeys = explode(',', auth()->user()?->api_keys);
            } else {
                $apiKeys = explode(',', $this->settings?->openai_api_secret);
            }
            $apiKey = $apiKeys[array_rand($apiKeys)];

            $len = strlen($apiKey);
            $parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        }

        return view('panel.user.openai.rewriter.index', compact(
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
        ));
    }

    public function fetchVideoTranscript($video_url): array
    {
        $video = isset($video_url) ? filter_var($video_url, FILTER_SANITIZE_URL) : null;
        if (! $video) {
            return [
                'error'  => __('Video URL is required!'),
                'code'   => 400,
                'status' => false,
            ];
        }
        // Get page
        $response = file_get_contents($video);
        if ($response === false) {
            return [
                'error'  => __('Failed to fetch video page.'),
                'code'   => 500,
                'status' => false,
            ];
        }

        // YouTube Player Response
        preg_match('/var ytInitialPlayerResponse = (.*?);<\/script>/s', $response, $matches);
        if (empty($matches[1])) {
            return [
                'error'  => __('Transcript data not found!'),
                'code'   => 404,
                'status' => false,
            ];
        }

        $dataString = trim($matches[1]);
        $data = json_decode($dataString, true);

        $captionsTrackList = isset($data['captions']['playerCaptionsTracklistRenderer']['captionTracks']) ?
        $data['captions']['playerCaptionsTracklistRenderer']['captionTracks'] : [];

        if (count($captionsTrackList) === 0) {
            return [
                'error'  => __('Transcript doesn\'t exist!'),
                'code'   => 404,
                'status' => false,
            ];
        }

        $captionsUrl = $captionsTrackList[0]['baseUrl'];

        // get transcript
        $captionsResponse = file_get_contents($captionsUrl);
        if ($captionsResponse === false) {
            return [
                'error'  => __('Failed to fetch captions.'),
                'code'   => 500,
                'status' => false,
            ];
        }

        // XML parse
        $xml = simplexml_load_string($captionsResponse);
        if ($xml === false) {
            return [
                'error'  => __('Failed to parse captions XML.'),
                'code'   => 500,
                'status' => false,
            ];
        }

        $transcript = '';
        foreach ($xml->text as $text_chunk) {
            $transcript .= $text_chunk . ' ';
        }

        return [
            'transcript' => $transcript,
            'code'       => 200,
            'status'     => true,
        ];
    }

    public function getYoutubeCaptions(Request $request)
    {
        $vidURL = $request->video_url;

        return (new youtubeTranscriptService)->getTranscript($vidURL);
    }
}
