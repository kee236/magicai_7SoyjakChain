<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AITokenType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    protected $fillable = [
        'type',
        'cost_per_token',
    ];

    protected $casts = [
        'type'           => AITokenType::class,
        'cost_per_token' => 'decimal:2',
    ];

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }
}
