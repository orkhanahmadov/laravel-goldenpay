<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Orkhanahmadov\LaravelGoldenpay\Tests\FakeSinglePayable::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(5),
    ];
});
