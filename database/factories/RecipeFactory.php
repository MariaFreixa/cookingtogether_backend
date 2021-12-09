<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
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

$factory->define(Recipe::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->unique()->safeEmail,
        'category' => $faker->randomElement(['admin', 'author', 'suscriptor']),
        'main_image' => now(),
        'diners' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'complexity' => Str::random(10),
        'video' => Str::random(10),
        'id_user' => factory(User::class)
    ];
});
