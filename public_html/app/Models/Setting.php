<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'free_open_ai_items' => 'array',
    ];

    protected static function booted(): void
    {
        static::updated(function ($model) {
            if ($model->recaptcha_login && (empty($model->recaptcha_sitekey) || empty($model->recaptcha_secretkey))) {
                $model->update([
                    'recaptcha_login' => false,
                ]);
            }

            if ($model->recaptcha_register && (empty($model->recaptcha_sitekey) || empty($model->recaptcha_secretkey))) {
                $model->update([
                    'recaptcha_register' => false,
                ]);
            }
        });
    }
}
