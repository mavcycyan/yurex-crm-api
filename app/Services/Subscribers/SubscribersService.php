<?php

namespace App\Services\Subscribers;

use App\Models\Finance\Subscriber\FinSubscriberPrice;
use App\Models\Subscriber\Subscriber;
use App\Services\Finances\Subscribers\FinSubscribersPaymentsService;
use App\Services\Finances\Subscribers\FinSubscribersPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class SubscribersService extends Service
{

    public static function index(Request $request) {
        $query = Subscriber::select('*');
        $input = $request->all();
        if (count($input) > 0) {
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $subscribers = $query->get()
            ->map(function ($work) {
                return [
                    'id' => $work->id,
                    'name' => $work->name,
                    'edrpou' => $work->edrpou,
                    'contract_date' => $work->contract_date,
                    'contact_person_name' => $work->contact_person_name,
                    'contact_person_phone' => $work->contact_person_phone,
                    'director_person_name' => $work->director_person_name,
                    'director_person_phone' => $work->director_person_phone,
                    'legal_address' => $work->legal_address,
                    'actual_address' => $work->actual_address,
                    'fee' => $work->latestFinSubscriberPrice->price ?? null,
                    'date_end' => $work->latestFinSubscriberPrice->date_end ?? null,
                ];
            });
        $count = Subscriber::select('*')->get()->count();
        return self::sendResponse($subscribers->toArray(), 'Subscribers returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $subscriber = Subscriber::find($id);
        $res = $subscriber;
        if (isset($subscriber->subscriber_works)) {
            $res->subscriber_works = $subscriber->subscriber_works->map(function ($item) {
                $item->what_done = unserialize($item->what_done);
                return $item;
            });
        }
        $res->fee = $res->latestFinSubscriberPrice->price ?? null;
        $res->date_end = $res->latestFinSubscriberPrice->date_end ?? null;
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
        if (isset($input['fee'])) {
            $fee = ($input['fee'] !== '') ? $input['fee'] : 0;
            $for_save = array('date' => $input['contract_date'], 'price' => $fee, 'subscriber_id' => $subscriber->id);
            if ($input['payment'] !== '' && $input['payment'] >= $fee) {
                $for_save['date_end'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            }
            $price = FinSubscribersPricesService::save_new($for_save);

            if (isset($input['payment']) && $price !== false) {
                $fee = ($input['payment'] !== '') ? $input['payment'] : 0;
                $payment = FinSubscribersPaymentsService::save_new(array('date' => $input['contract_date'], 'amount' => $fee, 'fin_subscriber_price_id' => $price->id));
            }
        }

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
        $subscriber->holder = $input['holder'];

        $subscriber->save();

        return self::sendResponse($subscriber, 'Subscriber "' . $subscriber->name . '" updated successfully.');
    }

    public static function destroy($id) {
        $subscriber = Subscriber::find($id);
        $subscriber_name = $subscriber->name;

        if (!$subscriber) {
            return self::sendError('Client not found.');
        }

        $works = $subscriber->subscriber_works;
        if (count($works) > 0) {
            foreach ($works as $work) {
                $work->delete();
            }
        }
        FinSubscriberPrice::where('subscriber_id', $id)
            ->each(function ($price_item) {
                $price_item->payments()->delete();
                $price_item->delete();
            });
        $subscriber->delete();

        return response()->json(['success' => true,'message' => 'Subscriber "' . $subscriber_name . '" deleted successfully.'], 200);
    }
}
