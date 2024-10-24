<?php

namespace App\Services\Subscribers;

use App\Models\Subscriber\SubscriberWork;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SubscriberWorksService extends Service
{
    public static function index(Request $request) {
        $subscribers = SubscriberWork::all();
        return self::sendResponse($subscribers->toArray(), 'Subscriber works returned successfully');
    }

    public static function show($id, Request $request) {
        $work = SubscriberWork::find($id);
        return self::sendResponse($work, 'Subscriber work returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'date' => 'required',
            'subscriber_id' => 'required'
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }
        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $work = SubscriberWork::create($input);
        return self::sendResponse($work, 'Subscriber work created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $work = SubscriberWork::find($id);

        $validator = Validator::make($input, [
            'date' => 'required',
            'subscriber_id' => 'required'
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }


        $work->date = Carbon::createFromFormat('Y-m-d', $input['date']);
        $work->what_done = $input['what_done'];
        $work->result = $input['result'];
        $work->subscriber_id = $input['subscriber_id'];

        $work->save();

        return self::sendResponse($work, 'Subscriber work updated successfully.');
    }

    public static function destroy($id) {
        $work = SubscriberWork::find($id);

        if (!$work) {
            return self::sendError('Subscriber work not found.');
        }
        $work->delete();

        return response()->json(['success' => true,'message' => 'Subscriber work deleted successfully.'], 200);
    }
}
