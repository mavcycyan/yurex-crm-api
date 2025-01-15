<?php

namespace App\Http\Controllers\Advocates;

use App\Http\Controllers\Controller;
use App\Services\Advocates\AdvocateCategoriesService;
use Illuminate\Http\Request;

class AdvocateCategoriesController extends Controller
{
    public function index(Request $request) {
        return AdvocateCategoriesService::index($request);
    }
    public function byslug(Request $request) {
        return AdvocateCategoriesService::byslug($request);
    }
    public function show($id, Request $request) {
        return AdvocateCategoriesService::show($id, $request);
    }
    public function store(Request $request) {
        return AdvocateCategoriesService::store($request);
    }
    public function update(Request $request, string $category) {
        return AdvocateCategoriesService::update($request, $category);
    }
    public function destroy($id) {
        return AdvocateCategoriesService::destroy($id);
    }
}
