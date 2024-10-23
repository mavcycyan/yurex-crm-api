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
    public function show($id, Request $request) {
        return SubscribersService::show($id, $request);
    }
    public function store(Request $request) {
        return SubscribersService::store($request);
    }
    public function update(Request $request, Subscriber $subscriber) {
        return SubscribersService::update($request, $subscriber);
    }
    public function destroy($id) {
        return SubscribersService::destroy($id);
    }
}
