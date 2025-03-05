<?php

namespace App\Services\Clients;

use App\Models\Client\Client;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientsService extends Service
{
    public static function index(Request $request) {
        $query = Client::select('*');
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
            if (isset($input['with_pending'])) {
                $query->withCount([
                    'client_works as pending_works' => function ($query) {
                        $query->where('result', 'pending');
                    }
                ]);
            }
        }
        $clients = $query->get()
            ->map(function ($work) {
                return [
                    'id' => $work->id,
                    'full_name' => $work->full_name,
                    'phone' => $work->phone,
                    'address' => $work->address,
                    'visit_date' => $work->oldestWork->visit_date ?? null,
                    'visit_purpose' => $work->oldestWork->visit_purpose ?? null,
                    'pending_works' => $work->pending_works
                ];
            });
        $count = Client::select('*')->get()->count();
        return self::sendResponse($clients->toArray(), 'Clients returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $client = Client::find($id);
        $res = $client;
        if (isset($client->client_works)) {
            $res->client_works = $client->client_works->map(function ($item) {
                $item->what_done = unserialize($item->what_done);
                return $item;
            });
        }
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
