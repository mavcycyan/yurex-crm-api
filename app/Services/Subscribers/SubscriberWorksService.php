<?php

namespace App\Services\Subscribers;

use App\Models\Auth\User;
use App\Models\Finance\Subscriber\FinSubscrWorkPrice;
use App\Models\Subscriber\SubscriberWork;
use App\Services\Finances\Subscribers\FinSubscrWorksPaymentsService;
use App\Services\Finances\Subscribers\FinSubscrWorksPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
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
        if (count($input) > 0) {
            $query = self::filtering($input, $query);
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $users = User::select('id', 'name')->get();
        $subscribers = $query->get()->map(function ($work) use($users) {
            $str = '';
            if ($work->worker && $work->worker !== null) {
                $ids = unserialize($work->worker);
                if (is_array($ids) && count($ids) > 0) {
                    $usrs = $users->whereIn('id', $ids);
                    if (count($usrs) > 0) {
                        $index = 0;
                        foreach ($usrs as $usr) {
                            if ($index > 0) {
                                $str .= ', ';
                            }
                            $str .= $usr->name;
                            $index++;
                        }
                    }
                }
            }
            $work->workers_names = $str;
            return [
                'id' => $work->id,
                'date' => $work->date,
                'date_end' => $work->date_end,
                'name' => $work->name,
                'result' => $work->result,
                'worker' => $work->worker,
                'workers_names' => $work->workers_names,
                'fee' => $work->latestFinSubscrWorkPrice->price ?? null,
                'fee_status' => $work->latestFinSubscrWorkPrice->status ?? null,
                'fee_currency' => $work->latestFinSubscrWorkPrice->price_cur ?? null,
                'subscriber_id' => $work->subscriber_id,
            ];
        });

        $count_query = SubscriberWork::where('subscriber_id', '=', $input['subscriber_id']);
        if (count($input) > 0) {
            $count_query = self::filtering($input, $count_query);
        }
        $count = $count_query->get()->count();
        return self::sendResponse($subscribers->toArray(), 'Subscriber works returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $work = SubscriberWork::find($id);

        $users = User::select('id', 'name')->get();
        $str = '';
        if ($work->worker && $work->worker !== null) {
            $ids = unserialize($work->worker);
            if (is_array($ids) && count($ids) > 0) {
                $usrs = $users->whereIn('id', $ids);
                if (count($usrs) > 0) {
                    $index = 0;
                    foreach ($usrs as $usr) {
                        if ($index > 0) {
                            $str .= ', ';
                        }
                        $str .= $usr->name;
                        $index++;
                    }
                }
            }
        }
        $work->workers_names = $str;

        $work->worker = unserialize($work->worker);
        $work->what_done = unserialize($work->what_done);
        $work->fee = $work->latestFinSubscrWorkPrice->price ?? null;
        $work->status = $work->latestFinSubscrWorkPrice->status ?? null;
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

        if (isset($input['worker']) && $input['worker'] !== '') {
            $worker = array();
            foreach ($input['worker'] as $wk) {
                $worker[] = $wk['id'];
            }
            $input['worker'] = serialize($worker);
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }

        $work = SubscriberWork::create($input);

        if (isset($input['fee'])) {
            $fee = ($input['fee'] !== '') ? $input['fee'] : 0;
            $for_save = array('date' => $input['date'], 'price' => $fee, 'subscriber_work_id' => $work->id);
            $price = FinSubscrWorksPricesService::save_new($for_save);

            if (isset($input['payment']) && $price !== false) {
                $fee = ($input['payment'] !== '') ? $input['payment'] : 0;
                $payment = FinSubscrWorksPaymentsService::save_new(array('date' => $input['date'], 'amount' => $fee, 'fin_subscr_work_price_id' => $price->id));
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

        if (isset($input['worker']) && $input['worker'] !== '') {
            $worker = array();
            foreach ($input['worker'] as $wk) {
                $worker[] = $wk['id'];
            }
            $input['worker'] = serialize($worker);
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }

        $work->date = $input['date'];
        $work->date_end = $input['date_end'];
        $work->name = $input['name'];
        $work->what_done = $input['what_done'];
        $work->result = $input['result'];
        $work->worker = $input['worker'];
        $work->subscriber_id = $input['subscriber_id'];

        $work->save();

        return self::sendResponse($work, 'Subscriber work updated successfully.');
    }

    public static function destroy($id) {
        $work = SubscriberWork::find($id);

        FinSubscrWorkPrice::where('subscriber_work_id', $id)
            ->each(function ($price_item) {
                $price_item->payments()->delete();
                $price_item->delete();
            });

        if (!$work) {
            return self::sendError('Subscriber work not found.');
        }
        $work->delete();

        return response()->json(['success' => true,'message' => 'Subscriber work deleted successfully.'], 200);
    }
}
