<?php

namespace App\Http\Controllers\Subscribers;

use App\Http\Controllers\Controller;
use App\Services\Subscribers\SubscriberWorksService;
use Illuminate\Http\Request;

class SubscriberWorksController extends Controller
{
    public function index(Request $request) {
        return SubscriberWorksService::index($request);
    }
    public function show($id, Request $request) {
        return SubscriberWorksService::show($id, $request);
    }
    public function store(Request $request) {
        return SubscriberWorksService::store($request);
    }
    public function update(Request $request, string $id) {
        return SubscriberWorksService::update($request, $id);
    }
    public function destroy($id) {
        return SubscriberWorksService::destroy($id);
    }
}
