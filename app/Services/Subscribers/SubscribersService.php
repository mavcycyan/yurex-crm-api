<?php

namespace App\Services\Subscribers;

use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscribersService extends Service
{

    public static function index(Request $request) {
        $subscribers = Subscriber::all();
        return self::sendResponse($subscribers->toArray(), 'Subscribers returned successfully');
    }

    public static function show($id, Request $request) {
        $subscriber = Subscriber::find($id);
        $res = $subscriber;
        if (isset($subscriber->subscriber_works)) {
            $res->subscriber_works = $subscriber->subscriber_works->map(function ($item) {
                $item->what_done = unserialize($item->what_done);
                return $item;
            });;
        }
        return self::sendResponse($res, 'Subscriber "' . $subscriber->name . '" returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        $subscriber = Subscriber::create($input);
        return self::sendResponse($subscriber, 'Subscriber created successfully');
    }

    public static function update(Request $request, Subscriber $subscriber) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        $subscriber->name = $input['name'];
        $subscriber->edrpou = $input['edrpou'];
        $subscriber->contract_date = $input['contract_date'];
        $subscriber->responsible_person_id = $input['responsible_person_id'];
        $subscriber->contact_person_name = $input['contact_person_name'];
        $subscriber->contact_person_phone = $input['contact_person_phone'];
        $subscriber->director_person_name = $input['director_person_name'];
        $subscriber->director_person_phone = $input['director_person_phone'];
        $subscriber->legal_address = $input['legal_address'];
        $subscriber->actual_address = $input['actual_address'];
        $subscriber->info = $input['info'];

        $subscriber->save();

        return self::sendResponse($subscriber, 'Subscriber "' . $subscriber->name . '" updated successfully.');
    }

    public static function destroy($id) {
        $subscriber = Subscriber::find($id);
        $subscriber_name = $subscriber->name;

        if (!$subscriber) {
            return self::sendError('Client not found.');
        }

//        $works = $subscriber->client_works;
//        if (count($works) > 0) {
//            foreach ($works as $work) {
//                $work->delete();
//            }
//        }
        $subscriber->delete();

        return response()->json(['success' => true,'message' => 'Subscriber "' . $subscriber_name . '" deleted successfully.'], 200);
    }
}
