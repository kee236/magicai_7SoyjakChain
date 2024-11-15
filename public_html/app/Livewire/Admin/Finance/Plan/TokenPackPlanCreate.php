<?php

namespace App\Livewire\Admin\Finance\Plan;

use App\Http\Controllers\Finance\PaymentProcessController;
use App\Models\OpenAIGenerator;
use App\Models\PaymentPlans;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Livewire\Component;

class TokenPackPlanCreate extends Component
{
    public string $currentStep = 'first';

    public string $name = '';

    public string $description = '';

    public string $features = '';

    public string $is_featured = '0';

    public string $plan_type = 'All';

    public float $price = 0.0;

    public string $currency = 'USD';

    public $is_team_plan = 0;

    public $plan_allow_seat = 0;

    public string $default_ai_model = 'gpt-3.5-turbo';

    public $is_trial = 0;

    public $trial_days = 7;

    public $total_words = 0;

    public $total_images = 0;

    public $display_imag_count = 1;

    public $display_word_count = 1;

    public $can_create_ai_images = 0;

    public $active = 1;

    public string $type = 'prepaid';

    public static $validation = [
        'name'               => 'required|string',
        'description'        => 'required|string',
        'price'              => 'required|numeric|min:0',
        'is_team_plan'       => 'required',
        'plan_allow_seat'    => 'required',
        'is_trial'           => 'required',
        'trial_days'         => 'required',
        'total_words'        => 'required',
        'total_images'       => 'required',
        'display_imag_count' => 'sometimes|nullable',
        'display_word_count' => 'sometimes|nullable',
    ];

    public function __construct(public $plan = null) {}

    public function mount($plan = null)
    {
        if ($plan->id) {
            $this->active = $plan->active;
            $this->name = $plan->name;
            $this->price = $plan->price;
            $this->is_free = $this->price > 0 ? 0 : 1;
            $this->currency = $plan->currency;
            $this->frequency = $plan->frequency;
            $this->is_featured = $plan->is_featured;
            $this->total_words = $plan->total_words;
            $this->total_images = $plan->total_images;
            $this->ai_name = $plan->ai_name;
            $this->can_create_ai_images = $plan->can_create_ai_images;
            $this->plan_type = $plan->plan_type;
            $this->features = $plan->features;
            $this->type = $plan->type;
            $this->is_team_plan = $plan->is_team_plan;
            $this->plan_allow_seat = $plan->plan_allow_seat;
            $this->trial_days = $plan->trial_days;
            $this->display_imag_count = $plan->display_imag_count;
            $this->display_word_count = $plan->display_word_count;
            $this->description = (string) $plan->description ?: '';
            $this->default_ai_model = $plan->default_ai_model;
        }
    }

    public function render(): Factory|Application|\Illuminate\Contracts\View\View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.finance.plan.token-pack-plan-create');
    }

    public function submit()
    {
        $this->validate(self::$validation);
        $data = $this->data();

        try {
            $requireUpdate = false;
            if ($this->plan->id) {
                if ($this->plan->price != (float) $data['price']) {
                    $requireUpdate = true;
                }
                $this->plan->update($data);
            } else {
                $requireUpdate = true;
                $this->plan = PaymentPlans::query()->create($data);
            }

            if ($requireUpdate) {
                PaymentProcessController::saveGatewayProducts($this->plan);
            }

            return redirect(route('dashboard.admin.finance.plan.index'))->with([
                'message' => 'Plan successfully updated.',
                'type'    => 'success',
            ]);
        } catch (Exception $e) {
            return;
        }
    }

    private function data(): array
    {
        $openAiItems = [];
        $openAIGenerators = OpenAIGenerator::query()
            ->where('active', true)
            ->get()
            ->groupBy('filters')
            ->filter(function ($item, $key) {
                return $key;
            });
        foreach ($openAIGenerators as $items) {
            foreach ($items as $generator) {
                $openAiItems[] = $generator->slug;
            }
        }

        return [
            'active'               => $this->active,
            'name'                 => $this->name,
            'price'                => $this->price,
            'currency'             => $this->currency,
            'is_featured'          => $this->is_featured,
            'is_free'              => $this->price > 0 ? 0 : 1,
            'stripe_product_id'    => null,
            'total_words'          => $this->total_words,
            'total_images'         => $this->total_images,
            'ai_name'              => 'AI',
            'can_create_ai_images' => true,
            'plan_type'            => $this->plan_type,
            'features'             => $this->features,
            'type'                 => $this->type,
            'is_team_plan'         => $this->is_team_plan,
            'plan_allow_seat'      => $this->plan_allow_seat,
            'trial_days'           => 0,
            'display_imag_count'   => $this->display_imag_count,
            'display_word_count'   => $this->display_word_count,
            'description'          => $this->description,
            'open_ai_items'        => $openAiItems,
            'default_ai_model'     => $this->default_ai_model,
        ];
    }
}
