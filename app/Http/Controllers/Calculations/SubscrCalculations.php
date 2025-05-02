<?php

namespace App\Http\Controllers\Calculations;

use App\Http\Controllers\Controller;
use App\Models\Calculations\SubscrCalculation;
use App\Services\Calculations\SubscrCalculations\SubscrCalculationsService;
use Illuminate\Http\Request;

class SubscrCalculations extends Controller
{
    public function index(Request $request) {
        return SubscrCalculationsService::index($request);
    }
    public function show($id, Request $request) {
        return SubscrCalculationsService::show($id, $request);
    }
    public function store(Request $request) {
        return SubscrCalculationsService::store($request);
    }
    public function update(Request $request, SubscrCalculation $subscriber) {
        return SubscrCalculationsService::update($request, $subscriber);
    }
}
