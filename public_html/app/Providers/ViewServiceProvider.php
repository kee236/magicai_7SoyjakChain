<?php

namespace App\Providers;

use App\Helpers\Classes\Helper;
use App\Models\FrontendSectionsStatusses;
use App\Models\FrontendSetting;
use App\Models\OpenAIGenerator;
use App\Models\Section\AdvancedFeaturesSection;
use App\Models\Section\BannerBottomText;
use App\Models\Section\ComparisonSectionItems;
use App\Models\Section\FeaturesMarquee;
use App\Models\Section\FooterItem;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use App\View\Composers\PlanComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public ?Setting $settings = null;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share app status
        $this->sharedAppStatus();

        // pagination
        Paginator::useBootstrap();

        if (! Helper::dbConnectionStatus()) {
            return;
        }

        if (! Schema::hasTable('migrations')) {
            return;
        }

        $this->shareSetting();

        $this->shareAiGenerator();

        //        $this->viewComposerShare();

        $this->goodForNowShare();

        View::composer(
            ['components.navbar.navbar'],
            PlanComposer::class
        );

    }

    public function goodForNowShare(): void
    {
        $goodForNow = false;

        if (Schema::hasTable('settings_two')) {
            $goodForNow = $this->settings && Helper::settingTwo('liquid_license_type');
        }

        View::share('good_for_now', $goodForNow);
    }

    public function viewComposerShare(): void
    {

        view()->composer('*', function ($view) {
            if (Auth::check()) {

                if (
                    ! Cache::has('total_words_' . Auth::id())
                    or ! Cache::has('total_documents_' . Auth::id())
                    or ! Cache::has('total_text_documents_' . Auth::id())
                    or ! Cache::has('total_image_documents_' . Auth::id())
                ) {
                    $total_documents_finder = UserOpenai::where('user_id', Auth::id())->get();
                    $total_words = UserOpenai::where('user_id', Auth::id())->sum('credits');
                    Cache::put('total_words_' . Auth::id(), $total_words, now()->addMinutes(360));
                    $total_documents = count($total_documents_finder);
                    Cache::put('total_documents_' . Auth::id(), $total_documents, now()->addMinutes(360));
                    $total_text_documents = count($total_documents_finder->where('credits', '!=', 1));
                    Cache::put('total_text_documents_' . Auth::id(), $total_text_documents, now()->addMinutes(360));
                    $total_image_documents = count($total_documents_finder->where('credits', '==', 1));
                    Cache::put('total_image_documents_' . Auth::id(), $total_image_documents, now()->addMinutes(360));
                }
                $total_words = Cache::get('total_words_' . Auth::id()) ?? 0;
                View::share('total_words', $total_words);
                $total_documents = Cache::get('total_documents_' . Auth::id()) ?? 0;
                View::share('total_documents', $total_documents);
                $total_text_documents = Cache::get('total_text_documents_' . Auth::id()) ?? 0;
                View::share('total_text_documents', $total_text_documents);
                $total_image_documents = Cache::get('total_image_documents_' . Auth::id()) ?? 0;
                View::share('total_image_documents', $total_image_documents);
            }
        });
    }

    public function shareAiGenerator(): void
    {
        if (! Schema::hasTable('openai')) {
            return;
        }

        View::share(
            'aiWriters',
            OpenAIGenerator::query()
                ->orderBy('title', 'asc')
                ->where('active', 1)
                ->get()
        );
    }

    public function shareSetting(): void
    {
        // general setting shared
        if ($settings = Setting::first()) {
            $this->settings = $settings;

            View::share('setting', $settings);
        }

        // frontend setting shared
        if (Schema::hasTable('frontend_footer_settings')) {

            $fSettings = FrontendSetting::first();

            if (! $fSettings) {
                $fSettings = new FrontendSetting;
                $fSettings->save();
            }

            View::share('fSetting', FrontendSetting::first());
        }

        // frontend sections status shared
        if (Schema::hasTable('frontend_sections_statuses_titles')) {

            $fSectSettings = FrontendSectionsStatusses::first();

            if (! $fSectSettings) {
                $fSectSettings = new FrontendSectionsStatusses;

                $fSectSettings->save();
            }

            View::share('fSectSettings', FrontendSectionsStatusses::first());
        }

        if (Schema::hasTable('openai')) {
            View::share('openAiList', OpenAIGenerator::query()->get());
        }

        // advanced_features_section
        if (Schema::hasTable('advanced_features_section')) {
            $advanced_features_section = AdvancedFeaturesSection::all();
            View::share('advanced_features_section', $advanced_features_section);
        }

        // comparison
        if (Schema::hasTable('comparison_section_items')) {
            $comparison_section_items = ComparisonSectionItems::all();
            View::share('comparison_section_items', $comparison_section_items);
        }

        // comparison
        if (Schema::hasTable('features_marquees')) {
            $top_marquee_items = FeaturesMarquee::where('position', 'top')->pluck('title')->toArray();
            $bottom_marquee_items = FeaturesMarquee::where('position', 'bottom')->pluck('title')->toArray();
            View::share('top_marquee_items', $top_marquee_items);
            View::share('bottom_marquee_items', $bottom_marquee_items);
        }

        // footer item

        if (Schema::hasTable('footer_items')) {
            $footer_items = FooterItem::query()->pluck('item')->toArray();
            View::share('footer_items', $footer_items);
        }
        // modern
        if (Schema::hasTable('banner_bottom_texts')) {
            $banner_bottom_texts = BannerBottomText::query()->pluck('text')->toArray();

            View::share('banner_bottom_texts', $banner_bottom_texts);
        }

        // frontend setting shared
        if (Schema::hasTable('settings_two')) {

            $settings_two = SettingTwo::first();

            if (! $settings_two) {
                $settings_two = new SettingTwo;
                $settings_two->save();
            }
            View::share('settings_two', $settings_two);
        }
    }

    public function sharedAppStatus(): void
    {
        View::share('app_is_demo', Helper::appIsDemo());
        View::share('app_is_not_demo', Helper::appIsNotDemo());
    }
}
