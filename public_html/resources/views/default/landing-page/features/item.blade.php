<x-box
    title="{!! __($item->title) !!}"
    desc="{!! __($item->description) !!}"
>

    <x-slot name="icon">
        @if(Str::endsWith($item->image, 'mp4'))
            <video width="100" height="100" controls>
                <source src="{{ asset('images/' . $item->image) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
{{--            <div style="background-image: url({{ asset('images/' . $item->image) }}); width: 100%; height: 100%">--}}
{{--                --}}
{{--            </div>--}}
            <a href="{{ url('/dashboard') }}">
                <img src="{{ asset('images/' . $item->image) }}" class="img-fluid" alt="Image" style="max-width: 300px; max-height: 300px;">
            </a>
        @endif
    </x-slot>

{{--    <x-slot name="icon">--}}
{{--        {!! $item->image !!}--}}
{{--    </x-slot>--}}
</x-box>
