<?php

namespace App\Http\Controllers\Finances\Subscribers;

use App\Http\Controllers\Controller;
use App\Services\Finances\Clients\FinClientWorksPricesService;
use App\Services\Finances\Subscribers\FinSubscrWorksPricesService;
use Illuminate\Http\Request;

class FinSubscrWorksPrices extends Controller
{
    public function index(Request $request) {
        return FinSubscrWorksPricesService::index($request);
    }
    public function show($id, Request $request) {
        return FinSubscrWorksPricesService::show($id, $request);
    }
    public function bulk_update(Request $request) {
        return FinSubscrWorksPricesService::bulk_update($request);
    }
}
