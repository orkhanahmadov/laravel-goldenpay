<?php

namespace Orkhanahmadov\LaravelGoldenpay\Tests;

use Illuminate\Http\Response;
use Orkhanahmadov\LaravelGoldenpay\Http\Controllers\GoldenpayController;

class FakeResultController extends GoldenpayController
{
    public function index(): Response
    {
        return response()->noContent(Response::HTTP_OK);
    }
}
