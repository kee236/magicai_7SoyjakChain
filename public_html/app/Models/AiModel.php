<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AITokenType;
use App\Models\Finance\AiChatModelPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    protected $fillable = [
        'title',
        'ai_engine',
        'key',
        'is_active',
        'is_selected',
        'selected_title',
    ];

    protected $casts = [
        'ai_engine' => \App\Enums\AIEngine::class,
        'is_active' => 'boolean',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    public function wordToken(): ?Token
    {
        return $this->tokens->firstWhere('type', AITokenType::WORD);
    }

    public function imageToken(): ?Token
    {
        return $this->tokens->firstWhere('type', AITokenType::IMAGE);
    }

    public function aiFinance()
    {
        return $this->hasMany(AiChatModelPlan::class, 'ai_model_id', 'id');
    }

    public static function planModels(): Collection|array
    {
        $planId = getCurrentActiveSubscription()?->getAttribute('plan_id') ?? 0;

        $query = self::query();

        if ($planId == 0) {
            $query->where('is_selected', 1);
        } else {
            $query->whereHas('aiFinance', function ($query) use ($planId) {
                $query->where('plan_id', $planId);
            });
        }

        $query->whereHas('tokens', function ($query) {
            $query->where('type', 'word');
        });

        return $query->get();
    }
}
