<?php

namespace App\Services\Finances\Subscribers;

use App\Models\Finance\Subscriber\FinSubscrWorkPayment;
use App\Models\Finance\Subscriber\FinSubscrWorkPrice;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FinSubscrWorksPricesService extends Service
{

    public static function index(Request $request) {

        if (
            isset($request['parent_id']) &&
            $request['parent_id'] !== ''
        ) {
            $prices = FinSubscrWorkPrice::with('payments')
            ->where('subscriber_work_id', $request['parent_id'])
            ->get();
        } else {
            $prices = FinSubscrWorkPrice::all();
        }
        return self::sendResponse($prices->toArray(), 'Prices and payments returned successfully');
    }

    public static function show($id, Request $request) {
        $price = FinSubscrWorkPrice::find($id);
        $res = $price;
        return self::sendResponse($res, 'Price with amount "' . $res->amount . '" returned successfully');
    }

    public static function store(Request $request) {

    }

    public static function update(Request $request, Subscriber $subscriber) {

    }

    public static function destroy($id) {

    }

    public static function save_new($input) {
        $validator = Validator::make($input, [
            'subscriber_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date_end'] = null;
        $payment = FinSubscrWorkPrice::create($input);
        return $payment;
    }

    public static function bulk_update(Request $request) {
        $input = $request->all();
        $subscriber_work_id = $input['parent_id'];

        if (count($input['prices']) > 0) {
            $prices = array();
            $prices_null = array();
            foreach ($input['prices'] as $pr) {
                if ($pr['id'] === null) {
                    $prices_null[] = $pr;
                }
                else {
                    $prices[] = $pr;
                }
            }
            if (count($prices) > 0) {
                foreach ($prices as $price) {
                    $update = array(
                        'date' => $price['date'],
                        'date_end' => $price['date_end'],
                        'price' => $price['price'],
                        'status' => $price['status']
                    );
                    FinSubscrWorkPrice::where('id', $price['id'])
                        ->update($update);

                    // payments
                    if (count($price['payments']) > 0) {
                        $payments = array();
                        $payments_null = array();
                        foreach ($price['payments'] as $pa) {
                            if ($pa['id'] === null) {
                                $payments_null[] = $pa;
                            }
                            else {
                                $payments[] = $pa;
                            }
                        }
                        if (count($payments) > 0) {
                            foreach ($payments as $pay) {
                                FinSubscrWorkPayment::where('id', $pay['id'])
                                    ->update(['date' => $pay['date'], 'amount' => $pay['amount']]);
                            }
                            $p_ids = collect($payments)->pluck('id')->toArray();
                            FinSubscrWorkPayment::whereNotIn('id', $p_ids)
                                ->where('fin_subscr_work_price_id', $price['id'])
                                ->delete();
                        }

                        if (count($payments_null) > 0) {
                            foreach ($payments_null as $pay) {
                                $data = array(
                                    'date' => $pay['date'],
                                    'amount' => $pay['amount'],
                                    'fin_subscr_work_price_id' => $price['id'],
                                );
                                FinSubscrWorkPayment::create($data);
                            }
                        }
                    } else {
                        FinSubscrWorkPayment::where('fin_subscr_work_price_id', $price['id'])
                            ->delete();
                    }

                }

                $ids = collect($prices)->pluck('id')->toArray();
                FinSubscrWorkPrice::whereNotIn('id', $ids)
                    ->where('subscriber_work_id', $subscriber_work_id)
                    ->each(function ($price_item) {
                        $price_item->payments()->delete();
                        $price_item->delete();
                    });
            }
            if (count($prices_null) > 0) {
                foreach ($prices_null as $price) {
                    $data_pr = array(
                        'date' => $price['date'],
                        'date_end' => $price['date_end'],
                        'price' => $price['price'],
                        'status' => $price['status'],
                        'subscriber_work_id' => $subscriber_work_id,
                    );
                    $new_pr = FinSubscrWorkPrice::create($data_pr);

                    // payments
                    if (count($price['payments']) > 0) {
                        foreach ($price['payments'] as $pay) {
                            $data = array(
                                'date' => $pay['date'],
                                'amount' => $pay['amount'],
                                'fin_subscr_work_price_id' => $new_pr->id,
                            );
                            FinSubscrWorkPayment::create($data);
                        }
                    }
                }
            }
        }
        else {
            FinSubscrWorkPrice::where('subscriber_work_id', $subscriber_work_id)
                ->each(function ($price_item) {
                    $price_item->payments()->delete();
                    $price_item->delete();
                });
        }


        $pr_db = FinSubscrWorkPrice::with('payments')
            ->where('subscriber_work_id', $subscriber_work_id)
            ->get();
        return self::sendResponse($pr_db->toArray(), 'Prices and payments updated successfully');
    }
}
