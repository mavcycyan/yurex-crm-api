<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client\ClientWork;
use App\Services\Clients\ClientWorksService;
use Illuminate\Http\Request;

class ClientWorksController extends Controller
{
    public function index(Request $request) {
        return ClientWorksService::index($request);
    }
    public function show($id, Request $request) {
        return ClientWorksService::show($id, $request);
    }
    public function store(Request $request) {
        return ClientWorksService::store($request);
    }
    public function update(Request $request, string $id) {
        return ClientWorksService::update($request, $id);
    }
    public function destroy($id) {
        return ClientWorksService::destroy($id);
    }
}
