<?php

use Faker\Generator as Faker;
use Orkhanahmadov\LaravelGoldenpay\Tests\FakeModel;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(FakeModel::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(5),
    ];
});
