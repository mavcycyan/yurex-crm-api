<?php

namespace App\Http\Controllers\Subscribers;

use App\Http\Controllers\Controller;
use App\Models\Subscriber\Subscriber;
use App\Services\Subscribers\SubscribersService;
use Illuminate\Http\Request;

class SubscribersController extends Controller
{

    public function index(Request $request) {
        return SubscribersService::index($request);
    }
    public function archive(Request $request) {
        return SubscribersService::archive($request);
    }
    public function show($id, Request $request) {
        return SubscribersService::show($id, $request);
    }
    public function store(Request $request) {
        return SubscribersService::store($request);
    }
    public function update(Request $request, Subscriber $subscriber) {
        return SubscribersService::update($request, $subscriber);
    }
    public function activity(Request $request, $id) {
        return SubscribersService::activity($request, $id);
    }
    public function update_stop_reason(Request $request, $id) {
        return SubscribersService::update_stop_reason($request, $id);
    }
    public function destroy($id) {
        return SubscribersService::destroy($id);
    }
}
