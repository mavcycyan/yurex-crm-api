<?php

namespace App\Http\Controllers\Finances\Advocates;

use App\Http\Controllers\Controller;
use App\Services\Finances\Advocates\FinAdvocateCasesPricesService;
use Illuminate\Http\Request;

class FinAdvocateCasesPrices extends Controller
{
    public function index(Request $request) {
        return FinAdvocateCasesPricesService::index($request);
    }
    public function show($id, Request $request) {
        return FinAdvocateCasesPricesService::show($id, $request);
    }
    public function bulk_update(Request $request) {
        return FinAdvocateCasesPricesService::bulk_update($request);
    }
}
