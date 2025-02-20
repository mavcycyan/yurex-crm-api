<?php

namespace App\Services\Subscribers;

use App\Models\Subscriber\SubscriberWork;
use App\Services\Finances\Subscribers\FinSubscrWorksPaymentsService;
use App\Services\Finances\Subscribers\FinSubscrWorksPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SubscriberWorksService extends Service
{
    public static function index(Request $request) {
        $input = $request->all();
        if (!isset($input['subscriber_id']) || $input['subscriber_id'] === '') {
            return self::sendError('No subscriber_id sent');
        }
        $query = SubscriberWork::select('*')
            ->where('subscriber_id', '=', $input['subscriber_id']);
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $subscribers = $query->get()->map(function ($work) {
            return [
                'id' => $work->id,
                'date' => $work->date,
                'name' => $work->name,
                'result' => $work->result,
                'fee' => $work->latestFinSubscrWorkPrice->price ?? null,
                'date_end' => $work->latestFinSubscrWorkPrice->date_end ?? null,
                'subscriber_id' => $work->subscriber_id,
            ];
        });
        $count = SubscriberWork::select('*')->get()->count();
        return self::sendResponse($subscribers->toArray(), 'Subscriber works returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $work = SubscriberWork::find($id);
        $work->what_done = unserialize($work->what_done);
        $work->fee = $work->latestFinSubscrWorkPrice->price ?? null;
        $work->date_end = $work->latestFinSubscrWorkPrice->date_end ?? null;
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
            $price = FinSubscrWorksPricesService::save_new($for_save);

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
        $work->name = $input['name'];
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
