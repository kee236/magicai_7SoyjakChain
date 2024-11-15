<x-navbar.item>
    <x-navbar.link label="{{ __('AI Plagiarism') }}"
        href="dashboard.user.openai.plagiarism.index" icon="tabler-progress-check"
        active-condition="{{ route('dashboard.user.openai.plagiarism.index') === url()->current() }}" />
</x-navbar.item>

<x-navbar.item>
    <x-navbar.link label="{{ __('AI Detector') }}"
        href="dashboard.user.openai.detectaicontent.index" icon="tabler-text-scan-2"
        active-condition="{{ route('dashboard.user.openai.detectaicontent.index') === url()->current() }}" />
</x-navbar.item>