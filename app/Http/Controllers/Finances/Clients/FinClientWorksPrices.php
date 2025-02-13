<?php

namespace App\Http\Controllers\Finances\Clients;

use App\Http\Controllers\Controller;
use App\Services\Finances\Clients\FinClientWorksPricesService;
use Illuminate\Http\Request;

class FinClientWorksPrices extends Controller
{
    public function index(Request $request) {
        return FinClientWorksPricesService::index($request);
    }
    public function show($id, Request $request) {
        return FinClientWorksPricesService::show($id, $request);
    }
    public function bulk_update(Request $request) {
        return FinClientWorksPricesService::bulk_update($request);
    }
}
