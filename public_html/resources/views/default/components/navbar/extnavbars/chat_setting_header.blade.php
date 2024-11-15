@if(
    \App\Models\Extension::query()->where('slug','chat-setting')->where('installed', true)->exists()
    && setting('chat_setting_for_customer', '1') == '1'

)
    <x-navbar.item has-dropdown>
        <x-navbar.link
                label="{{ __('Chat Settings') }}"
                href=""
                icon="tabler-message-circle"
                active-condition="{{ activeRouteBulk('dashboard.user.chat-setting.chat-category.*', 'dashboard.user.chat-setting.chatbot.*', 'dashboard.user.chat-setting.chat-template.*') }}"
                dropdown-trigger
        />
        <x-navbar.dropdown.dropdown
                open="{{ activeRouteBulk('dashboard.user.chat-setting.chat-category.*', 'dashboard.user.chat-setting.chatbot.*', 'dashboard.user.chat-setting.chat-template.*') }}"
        >
            <x-navbar.dropdown.item>
                <x-navbar.dropdown.link
                    label="{{ __('Chat Categories') }}"
                    href="dashboard.user.chat-setting.chat-category.index"
                >
                </x-navbar.dropdown.link>
            </x-navbar.dropdown.item>

            <x-navbar.dropdown.item>
                <x-navbar.dropdown.link
                    label="{{ __('Chat Templates') }}"
                    href="dashboard.user.chat-setting.chat-template.index"
                >
                </x-navbar.dropdown.link>
            </x-navbar.dropdown.item>

            <x-navbar.dropdown.item>
                <x-navbar.dropdown.link
                    label="{{ __('Chatbot Training') }}"
                    href="dashboard.user.chat-setting.chatbot.index"
                >
                </x-navbar.dropdown.link>
            </x-navbar.dropdown.item>
        </x-navbar.dropdown.dropdown>
    </x-navbar.item>
@endif

