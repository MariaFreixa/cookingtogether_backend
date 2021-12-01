<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model {
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_recipe', 'ingredient'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
}
