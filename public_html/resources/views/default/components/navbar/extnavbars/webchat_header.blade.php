<x-navbar.item>
    <x-navbar.link
            label="{{ __('AI Web Chat') }}"
            href="dashboard.user.openai.webchat.workbook"
            icon="tabler-world-www"
            active-condition="{{ route('dashboard.user.openai.webchat.workbook') == url()->current() ? 'active' : '' }}"
            new="true"
    />
</x-navbar.item>