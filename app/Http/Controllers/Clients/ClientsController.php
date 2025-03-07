<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Services\Clients\ClientsService;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index(Request $request) {
        return ClientsService::index($request);
    }
    public function show($id, Request $request) {
        return ClientsService::show($id, $request);
    }
    public function store(Request $request) {
        return ClientsService::store($request);
    }
    public function update($id, Request $request) {
        return ClientsService::update($id, $request);
    }
    public function destroy($id) {
        return ClientsService::destroy($id);
    }
}
