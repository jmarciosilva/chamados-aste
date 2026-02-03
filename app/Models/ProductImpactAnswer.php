<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImpactAnswer extends Model
{
    protected $fillable = ['impact_question_id', 'label', 'priority'];
}

