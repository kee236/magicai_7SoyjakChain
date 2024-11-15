<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'intro', 'order'];

    protected $casts = [
        'key' => \App\Enums\Introduction::class,
    ];

    public static function getFormattedSteps()
    {
        return self::orderBy('order')->get()->map(function ($item) {
            return [
                'intro'   => $item->intro,
                'element' => '[data-name="' . $item->key->value . '"]',
            ];
        });
    }
}
