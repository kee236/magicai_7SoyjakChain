@extends('panel.layout.settings')
@section('title', __('AI Avatar'))
@section('titlebar_subtitle',
    __('Create studio-quality videos with AI avatars and voiceovers in 130+ languages.
    It’s as easy as making a slide deck.'))
@section('settings')
    <form
            class="flex flex-col gap-5"
            id="synthesia_form"
            action="{{ route('dashboard.user.ai-avatar.store') }}"
            method="POST"
    >
        @csrf
        <x-form-step
                step="1"
                label="{{ __('Avatar') }}"
        />

        <div class="relative" x-data="{ open: false, selectedAvatar: null }">
            <label for="avatar" class="block text-sm text-gray-500 mb-2">
                {{ __('Avatar Name') }}
            </label>
            <div class="cursor-pointer rounded border p-2" @click="open = !open" :class="{ 'border-blue-500': open }">
                <template x-if="selectedAvatar">
                    <div class="flex items-center">
                        <img class="mr-2 h-10 w-auto" :src="`{{ asset('data/synthesia/avatars/') }}/${selectedAvatar.image}`" alt="">
                        <span x-text="selectedAvatar.name"></span>
                    </div>
                </template>
                <template x-if="!selectedAvatar">
                    <span>{{ __('Select an avatar') }}</span>
                </template>
            </div>
            <div class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto border bg-white" x-show="open" @click.away="open = false">
                <div class="grid grid-cols-2 gap-4 p-2">
                    @foreach ($avatars as $avatar)
                        <div class="flex flex-col cursor-pointer items-center hover:bg-gray-100 p-2" @click="selectedAvatar = { id: '{{ $avatar['avatar_id'] }}', name: '{{ $avatar['name'] }}', image: '{{ $avatar['image'] }}' }; open = false">
                            <img class="h-24 w-24 object-cover rounded-full shadow-md border border-gray-300" src="{{ asset('data/synthesia/avatars/' . $avatar['image']) }}" alt="{{ $avatar['name'] }}">
                            <div class="mt-2 text-center">
                                <span>{{ $avatar['name'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="avatar" :value="selectedAvatar ? selectedAvatar.id : ''">
        </div>

        <x-forms.input
                id="style"
                size="lg"
                type="select"
                label="{{ __('Avatar Style') }}"
                name="style"
                required
                onchange="toggleHorizontalAlign(this)"
        >
            <option value="rectangular">{{ __('Rectangular') }}</option>
            <option value="circular">{{ __('Circular') }}</option>
        </x-forms.input>

        <div
                id="horizontalAlignContainer"
                style="display: block;"
        >
            <x-forms.input
                    id="horizontalAlign"
                    size="lg"
                    type="select"
                    label="{{ __('Horizontal Align') }}"
                    name="horizontalAlign"
            >
                <option value="center">{{ __('Center') }}</option>
                <option value="left">{{ __('Left') }}</option>
                <option value="right">{{ __('Right') }}</option>
            </x-forms.input>
        </div>

        <x-forms.input
                id="c_color"
                label="{{ __('Background Color') }}"
                tooltip="{{ __('HEX color code (e.g. #F2F7FF) for the background of circular style avatar.') }}"
                type="color"
                name="backgroundColor"
                value="#8fd2d0"
                size="lg"
        />

        <x-form-step
                step="2"
                label="{{ __('Video') }}"
        >
        </x-form-step>

        <x-forms.input
                id="title"
                label="{{ __('Title') }}"
                size="lg"
                name="title"
                placeholder="{{ __('Title of the video to be shown on the share page.') }}"
                required
        />

        <x-forms.input
                id="description"
                label="{{ __('Description') }}"
                size="lg"
                name="description"
                placeholder="{{ __('Description of the video to be shown on the share page.') }}"
                required
        />

        <x-forms.input
                id="visibility"
                size="lg"
                type="select"
                label="{{ __('Visibility') }}"
                name="visibility"
        >
            <option value="public">{{ __('Public') }}</option>
            <option value="private">{{ __('Private') }}</option>
        </x-forms.input>

        <x-forms.input
                id="background"
                size="lg"
                type="select"
                label="{{ __('Video Background') }}"
                name="background"
        >
            <optgroup label="{{ __('Transparent') }}">
                @foreach ($backgrounds['transparent'] as $background)
                    <option value="{{ $background }}">{{ ucwords(str_replace('_', ' ', $background)) }}</option>
                @endforeach
            </optgroup>
            <optgroup label="{{ __('Solid') }}">
                @foreach ($backgrounds['solid'] as $background)
                    <option value="{{ $background }}">{{ ucwords(str_replace('_', ' ', $background)) }}</option>
                @endforeach
            </optgroup>
            <optgroup label="{{ __('Image') }}">
                @foreach ($backgrounds['image'] as $background)
                    <option value="{{ $background }}">{{ ucwords(str_replace('_', ' ', $background)) }}</option>
                @endforeach
            </optgroup>
        </x-forms.input>

        <x-forms.input
                id="scriptText"
                label="{{ __('Script Text') }}"
                placeholder="{{ __('Script for text to voice.') }}"
                type="textarea"
                rows="3"
                name="scriptText"
                required
        ></x-forms.input>

        <x-forms.input
                id="test"
                size="lg"
                type="select"
                tooltip="{{ __('Test videos are free and not counted towards your quota. If you create a video in the “test” mode, we will overlay a watermark over your video.') }}"
                label="{{ __('Test Status') }}"
                name="test"
        >
            <option value="true">{{ __('Enable') }}</option>
            <option value="false">{{ __('Disable') }}</option>
        </x-forms.input>

        @if ($app_is_demo)
            <x-button
                    type="button"
                    onclick="return toastr.info('This feature is disabled in Demo version.');"
            >
                {{ __('Generate Video') }}
            </x-button>
        @else
            <x-button
                    id="synthesia_btn"
                    type="submit"
                    form="synthesia_form"
            >
                {{ __('Generate Video') }}
            </x-button>
        @endif

    </form>
    <div id="preloader" class="spinner-border text-primary" role="status" style="display: none;width: 3rem; height: 3rem;">
        <span class="sr-only">Loading...</span>
    </div>
@endsection
<style>
    #preloader {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        z-index: 1000;
    }
</style>
@push('script')
    <script>
        document.getElementById('synthesia_form').addEventListener('submit', function(event) {
            document.getElementById('preloader').style.display = 'block';
        });
    </script>
    <script>
        function toggleHorizontalAlign(selectElement) {
            const horizontalAlignContainer = document.getElementById('horizontalAlignContainer');

            if (selectElement.value === 'rectangular') {
                horizontalAlignContainer.style.display = 'block';
            } else {
                horizontalAlignContainer.style.display = 'none';
            }
        }

        $(document).ready(function() {
            "use strict";

            const colorInput = document.querySelector('#c_color');
            const colorValue = document.querySelector('#c_color_value');

            colorInput?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorValue) {
                    colorValue.value = input.value
                };
            });

            colorValue?.addEventListener('input', ev => {
                const input = ev.currentTarget;

                if (colorInput) {
                    colorInput.value = input.value
                };
            });
        });
    </script>
@endpush
