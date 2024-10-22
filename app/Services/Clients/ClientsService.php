<?php

namespace App\Services\Clients;

use App\Models\Client\Client;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientsService extends Service
{
    public static function index(Request $request) {
        $clients = Client::all();
        return self::sendResponse($clients->toArray(), 'Clients returned successfully');
    }

    public static function show($id, Request $request) {
        $client = Client::find($id);
        $res = $client;
        $res->client_works = $client->client_works;
        return self::sendResponse($res, 'Client "' . $client->full_name . '" returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'full_name' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        $client = Client::create($input);
        return self::sendResponse($client, 'Client created successfully');
    }

    public static function update(Request $request, Client $client) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'full_name' => 'required',
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        $client->full_name = $input['full_name'];
        $client->where_knows = $input['where_knows'];
        $client->birthday = $input['birthday'];
        $client->address = $input['address'];
        $client->phone = $input['phone'];

        $client->save();

        return self::sendResponse($client, 'Client "' . $client->full_name . '" updated successfully.');
    }

    public static function destroy($id) {
        $client = Client::find($id);
        $client_name = $client->full_name;

        if (!$client) {
            return self::sendError('Client not found.');
        }

//        $works = ClientWork::select('id')
//            ->where('client_id', $id)
//            ->get();
        $works = $client->client_works;
        if (count($works) > 0) {
            foreach ($works as $work) {
                $work->delete();
            }
        }
        $client->delete();

        return response()->json(['success' => true,'message' => 'Client "' . $client_name . '" deleted successfully.'], 200);
    }
}
