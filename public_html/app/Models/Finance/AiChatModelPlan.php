<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AiChatModelPlan extends Pivot
{
    protected $table = 'ai_chat_model_plans';

    public $timestamps = false;

    protected $fillable = [
        'ai_model_id',
        'plan_id',
    ];
}
