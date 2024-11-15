<?php

namespace App\Models\Section;

use Illuminate\Database\Eloquent\Model;

class FeaturesMarquee extends Model
{
    protected $table = 'features_marquees';

    protected $fillable = [
        'title', 'position',
    ];
}
