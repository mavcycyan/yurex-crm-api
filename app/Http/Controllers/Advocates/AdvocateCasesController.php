<?php

namespace App\Http\Controllers\Advocates;

use App\Http\Controllers\Controller;
use App\Services\Advocates\AdvocateCasesService;
use Illuminate\Http\Request;

class AdvocateCasesController extends Controller
{
    public function index(Request $request) {
        return AdvocateCasesService::index($request);
    }
    public function show($id, Request $request) {
        return AdvocateCasesService::show($id, $request);
    }
    public function store(Request $request) {
        return AdvocateCasesService::store($request);
    }
    public function update(Request $request, string $case) {
        return AdvocateCasesService::update($request, $case);
    }
    public function destroy($id) {
        return AdvocateCasesService::destroy($id);
    }
}
