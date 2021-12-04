<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user', 'id_recipe', 'rating'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
}
