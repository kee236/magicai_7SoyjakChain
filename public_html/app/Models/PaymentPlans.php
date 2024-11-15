<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlans extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'active',
        'name',
        'price',
        'currency',
        'frequency',
        'is_featured',
        'is_free',
        'stripe_product_id',
        'total_words',
        'total_images',
        'ai_name',
        'max_tokens',
        'can_create_ai_images',
        'plan_type',
        'features',
        'type',
        'is_team_plan',
        'plan_allow_seat',
        'trial_days',
        'display_imag_count',
        'display_word_count',
        'open_ai_items',
        'description',
        'plan_ai_tools',
        'plan_features',
        'default_ai_model',
    ];

    protected $casts = [
        'open_ai_items' => 'json',
        'plan_ai_tools' => 'json',
        'plan_features' => 'json',
    ];

    // gateway_products
    public function gateway_products()
    {
        return $this->hasMany(GatewayProducts::class, 'plan_id', 'id');
    }

    // revenuecat_products
    public function revenuecat_products()
    {
        return $this->hasMany(RevenueCatProducts::class, 'plan_id', 'id');
    }

    public function checkOpenAiItemCount(): int
    {
        $items = is_array($this->open_ai_items) ? $this->open_ai_items : [];

        return count($items);
    }

    public function checkOpenAiItem($key): bool
    {
        $items = $this->open_ai_items ?: [];

        return in_array($key, $items);
    }
}
