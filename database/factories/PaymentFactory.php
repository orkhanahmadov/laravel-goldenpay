<?php

use Faker\Generator as Faker;
use Orkhanahmadov\Goldenpay\Enums\CardType;
use Orkhanahmadov\Goldenpay\Enums\Language;
use Orkhanahmadov\LaravelGoldenpay\Models\Payment;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Payment::class, function (Faker $faker) {
    return [
        'payment_key' => $faker->uuid,
        'amount' => random_int(50, 1000),
        'card_type' => $faker->randomElement(CardType::values()),
        'language' => $faker->randomElement(Language::values()),
        'description' => $faker->sentence,
        'status' => 1,
        'message' => $faker->sentence,
    ];
});
