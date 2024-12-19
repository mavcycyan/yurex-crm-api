<?php

namespace App\Services\Clients;

use App\Models\Client\ClientWork;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ClientWorksService extends Service
{
    public static function index(Request $request) {
        $works = ClientWork::with('client')
            ->get()
            ->map(function ($work) {
                return [
                    'id' => $work->id,
                    'visit_date' => $work->visit_date,
                    'visit_purpose' => $work->visit_purpose,
                    'where_knows' => $work->client->where_knows,
                    'full_name' => $work->client->full_name,
                    'phone' => $work->client->phone,
                    'result' => $work->result,
                    'fee' => $work->fee,
                    'client_id' => $work->client_id,
                ];
            });
        return self::sendResponse($works->toArray(), 'Client works returned successfully');
    }
    public static function get_by_client(Request $request) {
        $clients = ClientWork::all();
        return self::sendResponse($clients->toArray(), 'Client works returned successfully');
    }

    public static function show($id, Request $request) {
        $work = ClientWork::find($id);
        $work->what_done = unserialize($work->what_done);
        return self::sendResponse($work, 'Client work returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'visit_date' => 'required',
            'visit_purpose' => 'required',
            'client_id' => 'required'
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }

        $work = ClientWork::create($input);
        return self::sendResponse($work, 'Client work created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $work = ClientWork::find($id);

        $validator = Validator::make($input, [
            'visit_date' => 'required',
            'visit_purpose' => 'required',
            'client_id' => 'required'
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }


        $work->visit_date = Carbon::createFromFormat('Y-m-d', $input['visit_date']);
        $work->visit_purpose = $input['visit_purpose'];
        $work->what_done = $input['what_done'];
        $work->result = $input['result'];
        $work->fee = $input['fee'];
        $work->client_id = $input['client_id'];

        $work->save();

        return self::sendResponse($work, 'Client work updated successfully.');
    }

    public static function destroy($id) {
        $work = ClientWork::find($id);

        if (!$work) {
            return self::sendError('Client work not found.');
        }
        $work->delete();

        return response()->json(['success' => true,'message' => 'Client work deleted successfully.'], 200);
    }
}
