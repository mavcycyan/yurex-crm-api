<?php

namespace App\Http\Controllers\Advocates;

use App\Http\Controllers\Controller;
use App\Services\Advocates\AdvocateWorksService;
use Illuminate\Http\Request;

class AdvocateWorksController extends Controller
{
    public function index(Request $request) {
        return AdvocateWorksService::index($request);
    }
    public function show($id, Request $request) {
        return AdvocateWorksService::show($id, $request);
    }
    public function store(Request $request) {
        return AdvocateWorksService::store($request);
    }
    public function update(Request $request, string $case) {
        return AdvocateWorksService::update($request, $case);
    }
    public function destroy($id) {
        return AdvocateWorksService::destroy($id);
    }
}
