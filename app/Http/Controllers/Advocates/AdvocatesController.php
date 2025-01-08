<?php

namespace App\Http\Controllers\Advocates;

use App\Http\Controllers\Controller;
use App\Services\Advocates\AdvocatesService;
use Illuminate\Http\Request;

class AdvocatesController extends Controller
{
    public function index(Request $request) {
        return AdvocatesService::index($request);
    }
    public function show($id, Request $request) {
        return AdvocatesService::show($id, $request);
    }
    public function update(Request $request, string $case) {
        return AdvocatesService::update($request, $case);
    }
}
