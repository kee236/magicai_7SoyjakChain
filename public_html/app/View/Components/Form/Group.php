<?php

declare(strict_types=1);

namespace App\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;

class Group extends Component
{
    public function __construct(
        // public ViewErrorBag $errors,
        public ?string $id = null, // awareable
        public ?string $error = null, // awareable
        public ?string $setting = null, // awareable
        public ?string $help = null, // awareable
        public ?string $icon = null, // awareable
        public ?string $stepper = null, // awareable
        public ?string $action = null, // awareable
        public ?string $size = 'md',  // awareable
        public ?string $tooltip = null,  // awareable
        public ?string $label = null,  // awareable
        public bool $noGroupLabel = false,  // awareable
        public string $appendToLabel = ''
    ) {
        $this->handleErrors()
            ->applySettingField()
            ->applyLabel();
    }

    public function render(): View
    {
        return view('components.form.group');
    }

    private function applySettingField(): static
    {
        if (! $this->setting || $this->label) {
            return $this;
        }

        [$settingGroup, $settingKey] = explode('.', $this->setting);

        $this->label = setting()::group($settingGroup)->label($settingKey);

        return $this;

    }

    private function applyLabel(): void
    {
        if ($this->appendToLabel !== '') {
            $this->label .= ' ' . $this->appendToLabel;
        }
    }

    private function handleErrors(): static
    {
        if (
            ! $this->error &&
            $this->setting &&
            ($errors = view()->shared('errors')) &&
            $errors->has($this->setting)
        ) {
            $this->error = $errors->first($this->setting);
        }

        return $this;
    }
}
