<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Advocates\AdvocatesService;
use App\Services\Users\UsersService;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request) {
        return UsersService::index($request);
    }
    public function show($id, Request $request) {
        return UsersService::show($id, $request);
    }
    public function update(Request $request, $id) {
        return UsersService::update($request, $id);
    }
    public function activity(Request $request, $id) {
        return UsersService::activity($request, $id);
    }
    public function destroy($id) {
        return UsersService::destroy($id);
    }
    public function get_workers(Request $request) {
        return UsersService::get_workers($request);
    }
}
