<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Step;
use App\Recipe;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Step::class, function (Faker $faker) {
    return [
        'id_user' => factory(User::class),
        'id_recipe' => factory(Recipe::class),
        'step' => $faker->text
    ];
});
