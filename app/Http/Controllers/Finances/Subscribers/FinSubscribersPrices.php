<?php

namespace App\Http\Controllers\Finances\Subscribers;

use App\Http\Controllers\Controller;
use App\Services\Finances\Subscribers\FinSubscribersPricesService;
use Illuminate\Http\Request;

class FinSubscribersPrices extends Controller
{
    public function index(Request $request) {
        return FinSubscribersPricesService::index($request);
    }
    public function show($id, Request $request) {
        return FinSubscribersPricesService::show($id, $request);
    }
    public function bulk_update(Request $request) {
        return FinSubscribersPricesService::bulk_update($request);
    }
}
