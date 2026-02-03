<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImpactQuestion extends Model
{
    protected $fillable = ['product_id', 'question'];

    public function answers()
    {
        return $this->hasMany(ProductImpactAnswer::class, 'impact_question_id');
    }
}

