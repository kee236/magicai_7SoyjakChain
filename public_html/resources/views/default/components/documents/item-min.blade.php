@php
    $base_class .= ' flex gap-4 p-4 text-xs last:border-none';
@endphp

<a
	data-type="{{ trim($entry->generator->type) }}"
	{{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
	href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
>
	<x-lqd-icon
		class="lqd-docs-item-icon"
		size="lg"
		style="background: {{ $entry->generator->color }}"
	>
		<span class="size-5 flex">
			@if ($entry->generator->image !== 'none')
				{!! html_entity_decode($entry->generator->image) !!}
			@endif
		</span>
	</x-lqd-icon>
	<span class="block w-0 max-w-full grow overflow-hidden">
		<span class="lqd-docs-item-title block text-sm font-medium">
			{{ __($entry->generator->title) }}
		</span>
		<span class="lqd-docs-item-desc opacity-45 block w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic">
			{{ str()->words(__($entry->generator->description), 30) }}
		</span>
	</span>
	<span class="flex flex-col whitespace-nowrap">
		{{ __('in Workbook') }}
		<span class="lqd-docs-item-date opacity-45 italic">
			{{ $entry->created_at->format('M d, Y') }}
		</span>
	</span>
</a>