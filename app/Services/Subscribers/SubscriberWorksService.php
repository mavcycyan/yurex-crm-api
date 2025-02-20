<?php

namespace App\Services\Subscribers;

use App\Models\Subscriber\SubscriberWork;
use App\Services\Finances\Subscribers\FinSubscrWorksPaymentsService;
use App\Services\Finances\Subscribers\FinSubscriberWorksPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SubscriberWorksService extends Service
{
    public static function index(Request $request) {
        $query = SubscriberWork::select('*');
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $subscribers = $query->get();
        $count = SubscriberWork::select('*')->get()->count();
        return self::sendResponse($subscribers->toArray(), 'Subscriber works returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $work = SubscriberWork::find($id);
        $work->what_done = unserialize($work->what_done);
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

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }
        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $work = SubscriberWork::create($input);

        if (isset($input['fee'])) {
            $fee = ($input['fee'] !== '') ? $input['fee'] : 0;
            $for_save = array('date' => $input['date'], 'price' => $fee, 'subscriber_id' => $work->id);
            if ($input['payment'] !== '' && $input['payment'] >= $fee) {
                $for_save['date_end'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            }
            $price = FinSubscriberWorksPricesService::save_new($for_save);

            if (isset($input['payment']) && $price !== false) {
                $fee = ($input['payment'] !== '') ? $input['payment'] : 0;
                $payment = FinSubscrWorksPaymentsService::save_new(array('date' => $input['date'], 'amount' => $fee, 'fin_subscriber_work_price_id' => $price->id));
            }
        }

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


        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
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
