<!-- Start image generator -->
@if($openai->type == 'voiceover')
    <div class="row row-deck row-cards">
        <div class="col-12">
            <div class="card bg-[#F2F1FD] !shadow-sm dark:bg-[#14171C] dark:shadow-black">
                <div class="card-body md:p-10">
                    <div class="row">
                        <form id="openai_generator_form" class="workbook-form" onsubmit="return generateSpeech();">
                            <div class="relative mb-3">

                                <input
                                    type="text"
                                    id="workbook_title"
                                    class="form-control rounded-md"
                                    placeholder="{{__('Untitled Document...')}}"
                                    value="{{__('Untitled Document...')}}"
                                    required
                                >

                                <div class="absolute right-0 top-0 hidden">
                                    <a href="#advanced-settings" class="flex items-center text-[11px] font-semibold text-heading hover:no-underline group collapsed" data-bs-toggle="collapse" data-bs-auto-close="false">
                                        {{__('Advanced Settings')}}
                                        <span class="inline-flex items-center justify-center w-[36px] h-[36px] p-0 !ms-2 bg-white !shadow-sm rounded-full dark:!bg-[--tblr-bg-surface]">
                                            <svg class="hidden group-[&.collapsed]:block" width="12" height="12" viewBox="0 0 12 12" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.76708 5.464H11.1451V7.026H6.76708V11.558H5.18308V7.026H0.805078V5.464H5.18308V0.909999H6.76708V5.464Z"/>
                                            </svg>
                                            <svg class="block group-[&.collapsed]:hidden" width="6" height="2" viewBox="0 0 6 2" fill="var(--lqd-heading-color)" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.335078 1.962V0.246H5.65908V1.962H0.335078Z"/>
                                            </svg>
                                        </span>
                                    </a>
                                </div>

                            </div>
                            <hr>
                            <div class="flex flex-wrap justify-between gap-3 mt-8">
                                <div class="grow">
                                    <label for="language" class="form-label text-heading">
                                        {{__('Language')}}
                                        <x-info-tooltip text="{{__('Select speech language')}}" />
                                    </label>
                                    <select name="languages" id="languages" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        @if ($settings_two->tts == 'openai')
                                            <option abbr="Afr" value="afrikaans"> Afrikaans </option>
                                            <option abbr="Ar" value="arabic"> Arabic </option>
                                            <option abbr="Hy " value="armenian"> Armenian </option>
                                            <option abbr="Az" value="azerbaijani"> Azerbaijani </option>
                                            <option abbr="Be" value="belarusian"> Belarusian </option>
                                            <option abbr="Bs" value="bosnian"> Bosnian </option>
                                            <option abbr="Bg" value="bulgarian"> Bulgarian </option>
                                            <option abbr="Ca" value="catalan"> Catalan </option>
                                            <option abbr="Zh" value="chinese"> Chinese </option>
                                            <option abbr="Hr " value="croatian"> Croatian </option>
                                            <option abbr="Cs" value="czech"> Czech </option>
                                            <option abbr="Da" value="danish"> Danish </option>
                                            <option abbr="Nl" value="dutch"> Dutch </option>
                                            <option abbr="En" value="english" selected> English </option>
                                            <option abbr="Et" value="estonian"> Estonian </option>
                                            <option abbr="Fi" value="finnish"> Finnish </option>
                                            <option abbr="Fr" value="french"> French </option>
                                            <option abbr="Gl" value="galician"> Galician </option>
                                            <option abbr="De" value="german"> German </option>
                                            <option abbr="El" value="greek"> Greek </option>
                                            <option abbr="He" value="hebrew"> Hebrew </option>
                                            <option abbr="Hi" value="hindi"> Hindi </option>
                                            <option abbr="Hu" value="hungarian"> Hungarian </option>
                                            <option abbr="Is" value="icelandic"> Icelandic </option>
                                            <option abbr="Id" value="indonesian"> Indonesian </option>
                                            <option abbr="It" value="italian"> Italian </option>
                                            <option abbr="Ja" value="japanese"> Japanese </option>
                                            <option abbr="Kn" value="kannada"> Kannada </option>
                                            <option abbr="Kz" value="kazakh"> Kazakh </option>
                                            <option abbr="Ko" value="korean"> Korean </option>
                                            <option abbr="Lv" value="latvian"> Latvian </option>
                                            <option abbr="Lt" value="lithuanian"> Lithuanian </option>
                                            <option abbr="Mk" value="macedonian"> Macedonian </option>
                                            <option abbr="Ms" value="malay"> Malay </option>
                                            <option abbr="Mr" value="marathi"> Marathi </option>
                                            <option abbr="Ml" value="maori"> Maori </option>
                                            <option abbr="Ne" value="nepali"> Nepali </option>
                                            <option abbr="No" value="norwegian"> Norwegian </option>
                                            <option abbr="Fa" value="persian"> Persian </option>
                                            <option abbr="Pl" value="polish"> Polish </option>
                                            <option abbr="Pt" value="portuguese"> Portuguese </option>
                                            <option abbr="Ro" value="romanian"> Romanian </option>
                                            <option abbr="Ru" value="russian"> Russian </option>
                                            <option abbr="Sr" value="serbian"> Serbian </option>
                                            <option abbr="Sk" value="slovak"> Slovak </option>
                                            <option abbr="Sl" value="slovenian"> Slovenian </option>
                                            <option abbr="Es" value="spanish"> Spanish </option>
                                            <option abbr="Sw" value="swahili"> Swahili </option>
                                            <option abbr="Sv" value="swedish"> Swedish </option>
                                            <option abbr="Tl" value="tagalog"> Tagalog </option>
                                            <option abbr="Ta" value="tamil"> Tamil </option>
                                            <option abbr="Th" value="thai"> Thai </option>
                                            <option abbr="Tr" value="turkish"> Turkish </option>
                                            <option abbr="Uk" value="ukrainian"> Ukrainian </option>
                                            <option abbr="Ur" value="urdu"> Urdu </option>
                                            <option abbr="Vi" value="vietnamese"> Vietnamese </option>
                                            <option abbr="Cy" value="welsh"> Welsh </option>
                                        @else
                                            <option language="Afrikaans" language="afrikaans" @if(LaravelLocalization::getCurrentLocale() == "af") {{ 'selected' }} @endif value="af-ZA">{{__('Afrikaans (South Africa)')}}</option>
                                            <option language="Arabic" @if(LaravelLocalization::getCurrentLocale() == "ar") {{ 'selected' }} @endif value="ar-XA">{{__('Arabic')}}</option>
                                            <option language="Basque" @if(LaravelLocalization::getCurrentLocale() == "eu") {{ 'selected' }} @endif value="eu-ES">{{__('Basque (Spain)')}}</option>
                                            <option language="Bengali" @if(LaravelLocalization::getCurrentLocale() == "bn") {{ 'selected' }} @endif value="bn-IN">{{__('Bengali (India)')}}</option>
                                            <option language="Bulgarian" @if(LaravelLocalization::getCurrentLocale() == "bg") {{ 'selected' }} @endif value="bg-BG">{{__('Bulgarian (Bulgaria)')}}</option>
                                            <option language="Catalan" @if(LaravelLocalization::getCurrentLocale() == "ca") {{ 'selected' }} @endif value="ca-ES">{{__('Catalan (Spain) ')}}</option>
                                            <option language="Chinese" @if(LaravelLocalization::getCurrentLocale() == "yue") {{ 'selected' }} @endif value="yue-HK">{{__('Chinese (Hong Kong)')}}</option>
                                            <option language="Czech" @if(LaravelLocalization::getCurrentLocale() == "cs") {{ 'selected' }} @endif value="cs-CZ">{{__('Czech (Czech Republic)')}}</option>
                                            <option language="Danish" @if(LaravelLocalization::getCurrentLocale() == "da") {{ 'selected' }} @endif value="da-DK">{{__('Danish (Denmark)')}}</option>
                                            <option language="Dutch" @if(LaravelLocalization::getCurrentLocale() == "nl") {{ 'selected' }} @endif value="nl-BE">{{__('Dutch (Belgium)')}}</option>
                                            <option language="Dutch" value="nl-NL">{{__('Dutch (Netherlands)')}}</option>
                                            <option language="English" value="en-AU">{{__('English (Australia)')}}</option>
                                            <option language="English" value="en-IN">{{__('English (India)')}}</option>
                                            <option language="English" value="en-GB">{{__('English (UK)')}}</option>
                                            <option language="English" @if(LaravelLocalization::getCurrentLocale() == "en") {{ 'selected' }} @endif value="en-US">{{__('English (US)')}}</option>
                                            <option language="Filipino" @if(LaravelLocalization::getCurrentLocale() == "fil") {{ 'selected' }} @endif value="fil-PH">{{__('Filipino (Philippines)')}}</option>
                                            <option language="Finnish" @if(LaravelLocalization::getCurrentLocale() == "fi") {{ 'selected' }} @endif value="fi-FI">{{__('Finnish (Finland)')}}</option>
                                            <option language="French" value="fr-CA">{{__('French (Canada)')}}</option>
                                            <option language="French" @if(LaravelLocalization::getCurrentLocale() == "fr") {{ 'selected' }} @endif value="fr-FR">{{__('French (France)')}}</option>
                                            <option language="Galician" @if(LaravelLocalization::getCurrentLocale() == "gl") {{ 'selected' }} @endif value="gl-ES">{{__('Galician (Spain)')}}</option>
                                            <option language="German" @if(LaravelLocalization::getCurrentLocale() == "de") {{ 'selected' }} @endif value="de-DE">{{__('German (Germany)')}}</option>
                                            <option language="Greek" @if(LaravelLocalization::getCurrentLocale() == "el") {{ 'selected' }} @endif value="el-GR">{{__('Greek (Greece)')}}</option>
                                            <option language="Gujarati" @if(LaravelLocalization::getCurrentLocale() == "gu") {{ 'selected' }} @endif value="gu-IN">{{__('Gujarati (India)')}}</option>
                                            <option language="Hebrew" @if(LaravelLocalization::getCurrentLocale() == "he") {{ 'selected' }} @endif value="he-IL">{{__('Hebrew (Israel)')}}</option>
                                            <option language="Hindi" @if(LaravelLocalization::getCurrentLocale() == "hi") {{ 'selected' }} @endif value="hi-IN">{{__('Hindi (India)')}}</option>
                                            <option language="Hungarian" @if(LaravelLocalization::getCurrentLocale() == "hu") {{ 'selected' }} @endif value="hu-HU">{{__('Hungarian (Hungary)')}}</option>
                                            <option language="Icelandic" @if(LaravelLocalization::getCurrentLocale() == "is") {{ 'selected' }} @endif value="is-IS">{{__('Icelandic (Iceland)')}}</option>
                                            <option language="Indonesian" @if(LaravelLocalization::getCurrentLocale() == "id") {{ 'selected' }} @endif value="id-ID">{{__('Indonesian (Indonesia)')}}</option>
                                            <option language="Italian" @if(LaravelLocalization::getCurrentLocale() == "it") {{ 'selected' }} @endif value="it-IT">{{__('Italian (Italy)')}}</option>
                                            <option language="Japanese" @if(LaravelLocalization::getCurrentLocale() == "ja") {{ 'selected' }} @endif value="ja-JP">{{__('Japanese (Japan)')}}</option>
                                            <option language="Kannada" @if(LaravelLocalization::getCurrentLocale() == "kn") {{ 'selected' }} @endif value="kn-IN">{{__('Kannada (India)')}}</option>
                                            <option language="Korean (Sout" @if(LaravelLocalization::getCurrentLocale() == "ko") {{ 'selected' }} @endif value="ko-KR">{{__('Korean (South Korea)')}}</option>
                                            <option language="Latvian" @if(LaravelLocalization::getCurrentLocale() == "lv") {{ 'selected' }} @endif value="lv-LV">{{__('Latvian (Latvia)')}}</option>
                                            <option language="Malay" @if(LaravelLocalization::getCurrentLocale() == "ms") {{ 'selected' }} @endif value="ms-MY">{{__('Malay (Malaysia)')}}</option>
                                            <option language="Malayalam" @if(LaravelLocalization::getCurrentLocale() == "ml") {{ 'selected' }} @endif value="ml-IN">{{__('Malayalam (India)')}}</option>
                                            <option language="Mandari" @if(LaravelLocalization::getCurrentLocale() == "cmn") {{ 'selected' }} @endif value="cmn-CN">{{__('Mandarin Chinese')}}</option>
                                            <option language="Mandarin Chinese" value="cmn-TW">{{__('Mandarin Chinese (T)')}}</option>
                                            <option language="Marathi" @if(LaravelLocalization::getCurrentLocale() == "mr") {{ 'selected' }} @endif value="mr-IN">{{__('Marathi (India)')}}</option>
                                            <option language="Norwegian" @if(LaravelLocalization::getCurrentLocale() == "nb") {{ 'selected' }} @endif value="nb-NO">{{__('Norwegian (Norway)')}}</option>
                                            <option language="Polish" @if(LaravelLocalization::getCurrentLocale() == "pl") {{ 'selected' }} @endif value="pl-PL">{{__('Polish (Poland)')}}</option>
                                            <option language="Portuguese" @if(LaravelLocalization::getCurrentLocale() == "pt") {{ 'selected' }} @endif value="pt-BR">{{__('Portuguese (Brazil)')}}</option>
                                            <option language="Portuguese" value="pt-PT">{{__('Portuguese (Portugal)')}}</option>
                                            <option language="Punjabi" @if(LaravelLocalization::getCurrentLocale() == "pa") {{ 'selected' }} @endif value="pa-IN">{{__('Punjabi (India)')}}</option>
                                            <option language="Romanian" @if(LaravelLocalization::getCurrentLocale() == "ro") {{ 'selected' }} @endif value="ro-RO">{{__('Romanian (Romania)')}}</option>
                                            <option language="Russian" @if(LaravelLocalization::getCurrentLocale() == "ru") {{ 'selected' }} @endif value="ru-RU">{{__('Russian (Russia)')}}</option>
                                            <option language="Serbian" @if(LaravelLocalization::getCurrentLocale() == "sr") {{ 'selected' }} @endif value="sr-RS">{{__('Serbian (Cyrillic)')}}</option>
                                            <option language="Slovak" @if(LaravelLocalization::getCurrentLocale() == "sk") {{ 'selected' }} @endif value="sk-SK">{{__('Slovak (Slovakia)')}}</option>
                                            <option language="Spanish" @if(LaravelLocalization::getCurrentLocale() == "es") {{ 'selected' }} @endif value="es-ES">{{__('Spanish (Spain)')}}</option>
                                            <option language="Spanish" @if(LaravelLocalization::getCurrentLocale() == "es") {{ 'selected' }} @endif value="es-US">{{__('Spanish (US)')}}</option>
                                            <option language="Swedish" @if(LaravelLocalization::getCurrentLocale() == "sv") {{ 'selected' }} @endif value="sv-SE">{{__('Swedish (Sweden)')}}</option>
                                            <option language="Tamil" @if(LaravelLocalization::getCurrentLocale() == "ta") {{ 'selected' }} @endif value="ta-IN">{{__('Tamil (India)')}}</option>
                                            <option language="Telugu" @if(LaravelLocalization::getCurrentLocale() == "te") {{ 'selected' }} @endif value="te-IN">{{__('Telugu (India)')}}</option>
                                            <option language="Thai" @if(LaravelLocalization::getCurrentLocale() == "th") {{ 'selected' }} @endif value="th-TH">{{__('Thai (Thailand)')}}</option>
                                            <option language="Turkish" @if(LaravelLocalization::getCurrentLocale() == "tr") {{ 'selected' }} @endif value="tr-TR">{{__('Turkish (Turkey)')}}</option>
                                            <option language="Ukrainian" @if(LaravelLocalization::getCurrentLocale() == "uk") {{ 'selected' }} @endif value="uk-UA">{{__('Ukrainian (Ukraine)')}}</option>
                                            <option language="Vietnamese" @if(LaravelLocalization::getCurrentLocale() == "vi") {{ 'selected' }} @endif value="vi-VN">{{__('Vietnamese (Vietnam)')}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if ( $settings_two->tts == 'openai')
                                <div class="grow">
                                    <label for="voice_openai" class="form-label text-heading">
                                        {{__('Voice')}}
                                        <x-info-tooltip text="{{__('You can select speech voice')}}" />
                                    </label>
                                    <select id="voice_openai" name="voice" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        <option value="alloy">{{ __('Alloy') }}</option>
                                        <option value="echo">{{ __('Echo') }}</option>
                                        <option value="fable">{{ __('Fable') }}</option>
                                        <option value="onyx">{{ __('Onyx') }}</option>
                                        <option value="nova">{{ __('Nova') }}</option>
                                        <option value="shimmer">{{ __('shimmer') }}</option>
                                    </select>
                                </div>
                                <div class="grow">
                                    <label for="model_openai" class="form-label text-heading">
                                        {{__('Model')}}
                                        <x-info-tooltip text="{{__('You can select speech model')}}" />
                                    </label>
                                    <select id="model_openai" name="model_openai" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        <option value="tts-1">{{ __('TTS-1') }}</option>
                                        <option value="tts-1-hd">{{ __('TTS-1-HD') }}</option>
                                    </select>
                                </div>
                                @else
                                <div class="grow">
                                    <label for="voice" class="form-label text-heading">
                                        {{__('Voice')}}
                                        <x-info-tooltip text="{{__('You can select speech voice. Female, Male, and type')}}" />
                                    </label>
                                    <select id="voice" name="voice" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        <option value="">{{ __('Select a voice') }}</option>
                                    </select>
                                </div>
                                <div class="grow">
                                    <label for="pace" class="form-label text-heading">
                                        {{__('Pace')}}
                                        <x-info-tooltip text="{{__('Speech speed')}}" />
                                    </label>
                                    <select id="pace" name="pace" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        <option value="x-slow">{{ __('Very Slow') }}</option>
                                        <option value="slow">{{ __('Slow') }}</option>
                                        <option value="medium" selected>{{ __('Medium') }}</option>
                                        <option value="fast">{{ __('Fast') }}</option>
                                        <option value="x-fast">{{ __('Ultra Fast') }}</option>
                                    </select>
                                </div>
                                <div class="grow">
                                    <label for="break" class="form-label text-heading">
                                        {{__('Pause')}}
                                        <x-info-tooltip text="{{__('Wait x seconds after the speech. Represents the time before the next sentence.')}}" />
                                    </label>
                                    <select id="break" name="break" class="form-control form-select bg-[#fff] placeholder:text-black">
                                        <option value="0">{{ __('0s') }}</option>
                                        <option value="1" selected>{{ __('1s') }}</option>
                                        <option value="2">{{ __('2s') }}</option>
                                        <option value="3">{{ __('3s') }}</option>
                                        <option value="4">{{ __('4s') }}</option>
                                    </select>
                                </div>
                                @endif
                            </div>

                            <div id="advanced-settings" class="collapse">
                                <div class="flex flex-wrap justify-between gap-3 mt-8">
                                    <div class="grow">
                                        <label for="image_style" class="form-label text-heading">{{__('Art Style')}}</label>
                                        <select name="image_style" id="image_style" class="form-control form-select bg-[#fff] placeholder:text-black">
                                            <option value="" selected="selected">{{__('None')}}</option>
                                            <option value="3d_render">{{__('3D Render')}}</option>
                                            <option value="anime">{{__('Anime')}}</option>
                                            <option value="ballpoint_pen">{{__('Ballpoint Pen Drawing')}}</option>
                                            <option value="bauhaus">{{__('Bauhaus')}}</option>
                                            <option value="cartoon">{{__('Cartoon')}}</option>
                                            <option value="clay">{{__('Clay')}}</option>
                                            <option value="contemporary">{{__('Contemporary')}}</option>
                                            <option value="cubism">{{__('Cubism')}}</option>
                                            <option value="cyberpunk">{{__('Cyberpunk')}}</option>
                                            <option value="glitchcore">{{__('Glitchcore')}}</option>
                                            <option value="impressionism">{{__('Impressionism')}}</option>
                                            <option value="isometric">{{__('Isometric')}}</option>
                                            <option value="line">{{__('Line Art')}}</option>
                                            <option value="low_poly">{{__('Low Poly')}}</option>
                                            <option value="minimalism">{{__('Minimalism')}}</option>
                                            <option value="modern">{{__('Modern')}}</option>
                                            <option value="origami">{{__('Origami')}}</option>
                                            <option value="pencil">{{__('Pencil Drawing')}}</option>
                                            <option value="pixel">{{__('Pixel')}}</option>
                                            <option value="pointillism">{{__('Pointillism')}}</option>
                                            <option value="pop">{{__('Pop')}}</option>
                                            <option value="realistic">{{__('Realistic')}}</option>
                                            <option value="renaissance">{{__('Renaissance')}}</option>
                                            <option value="retro">{{__('Retro')}}</option>
                                            <option value="steampunk">{{__('Steampunk')}}</option>
                                            <option value="sticker">{{__('Sticker')}}</option>
                                            <option value="ukiyo">{{__('Ukiyo')}}</option>
                                            <option value="vaporwave">{{__('Vaporwave')}}</option>
                                            <option value="vector">{{__('Vector')}}</option>
                                            <option value="watercolor">{{__('Watercolor')}}</option>
                                        </select>
                                    </div>
                                    <div class="grow">
                                        <label for="image_lighting" class="form-label text-heading">{{__('Lightning Style')}}</label>
                                        <select id="image_lighting" name="image_lighting" class="form-control form-select bg-[#fff] placeholder:text-black">
                                            <option value="" selected="selected">{{ __('None') }}</option>
                                            <option value="ambient">{{ __('Ambient') }}</option>
                                            <option value="backlight">{{ __('Backlight') }}</option>
                                            <option value="blue_hour">{{ __('Blue Hour') }}</option>
                                            <option value="cinematic">{{ __('Cinematic') }}</option>
                                            <option value="cold">{{ __('Cold') }}</option>
                                            <option value="dramatic">{{ __('Dramatic') }}</option>
                                            <option value="foggy">{{ __('Foggy') }}</option>
                                            <option value="golden_hour">{{ __('Golden Hour') }}</option>
                                            <option value="hard">{{ __('Hard') }}</option>
                                            <option value="natural">{{ __('Natural') }}</option>
                                            <option value="neon">{{ __('Neon') }}</option>
                                            <option value="studio">{{ __('Studio') }}</option>
                                            <option value="warm">{{ __('Warm') }}</option>
                                        </select>
                                    </div>
                                    <div class="grow">
                                        <label for="image_mood" class="form-label text-heading">{{__('Mood')}}</label>
                                        <select id="image_mood" name="image_mood" class="form-control form-select bg-[#fff] placeholder:text-black">
                                            <option value="" selected="selected">{{ __('None') }}</option>
                                            <option value="aggressive">{{ __('Aggressive') }}</option>
                                            <option value="angry">{{ __('Angry') }}</option>
                                            <option value="boring">{{ __('Boring') }}</option>
                                            <option value="bright">{{ __('Bright') }}</option>
                                            <option value="calm">{{ __('Calm') }}</option>
                                            <option value="cheerful">{{ __('Cheerful') }}</option>
                                            <option value="chilling">{{ __('Chilling') }}</option>
                                            <option value="colorful">{{ __('Colorful') }}</option>
                                            <option value="dark">{{ __('Dark') }}</option>
                                            <option value="neutral">{{ __('Neutral') }}</option>
                                        </select>
                                    </div>
                                    <div class="grow">
                                        <label for="image_number_of_images" class="form-label text-heading">{{__('Number of Images')}}</label>
                                        <select name="image_number_of_images" id="image_number_of_images" class="form-control form-select bg-[#fff] placeholder:text-black">
                                            <option value="1" selected="selected">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center space-x-3 mb-2">
                                    <label class="form-label text-heading m-0">{{__('Speeches')}}</label>
                                    <button type="button" class="btn add-new-text text-[12px] bg-[#DAFBEE] rounded-md m-0 py-1 px-2 dark:bg-teal-950">+ {{__('Add New')}}</button>
                                </div>
                                <div class="speeches"></div>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary" id="generate_speech_button">{{__('Generate')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="generator_sidebar_table">
        @include('panel.user.openai.generator_components.generator_sidebar_table')
        </div>
    </div>
@endif
<!-- End image generator -->
