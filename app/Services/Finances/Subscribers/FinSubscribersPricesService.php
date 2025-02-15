<?php

namespace App\Services\Finances\Subscribers;

use App\Models\Finance\Subscriber\FinSubscriberPayment;
use App\Models\Finance\Subscriber\FinSubscriberPrice;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FinSubscribersPricesService extends Service
{

    public static function index(Request $request) {

        if (
            isset($request['parent_id']) &&
            $request['parent_id'] !== ''
        ) {
            $prices = FinSubscriberPrice::with('payments')
            ->where('subscriber_id', $request['parent_id'])
            ->get();
        } else {
            $prices = FinSubscriberPrice::all();
        }
        return self::sendResponse($prices->toArray(), 'Prices and payments returned successfully');
    }

    public static function show($id, Request $request) {
        $price = FinSubscriberPrice::find($id);
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
            'subscriber_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $input['date_end'] = null;
        $payment = FinSubscriberPrice::create($input);
        return $payment;
    }

    public static function bulk_update(Request $request) {
        $input = $request->all();
        $subscriber_id = $input['parent_id'];

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
                    $update = array('price' => $price['price']);
                    if (count($price['payments']) > 0) {
                        $amounts = collect($price['payments'])->pluck('amount')->toArray();
                        $amount_sum = 0;
                        foreach ($amounts as $amount) {
                            $amount_sum += (int) $amount;
                        }
                        if ($amount_sum >= $price['price']) {
                            $update['date_end'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        }
                    }
                    FinSubscriberPrice::where('id', $price['id'])
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
                                FinSubscriberPayment::where('id', $pay['id'])
                                    ->update(['date' => $pay['date'], 'amount' => $pay['amount']]);
                            }
                            $p_ids = collect($payments)->pluck('id')->toArray();
                            FinSubscriberPayment::whereNotIn('id', $p_ids)
                                ->where('fin_subscriber_price_id', $price['id'])
                                ->delete();
                        }

                        if (count($payments_null) > 0) {
                            foreach ($payments_null as $pay) {
                                $data = array(
                                    'date' => $pay['date'],
                                    'amount' => $pay['amount'],
                                    'fin_subscriber_price_id' => $price['id'],
                                );
                                FinSubscriberPayment::create($data);
                            }
                        }
                    } else {
                        FinSubscriberPayment::where('fin_subscriber_price_id', $price['id'])
                            ->delete();
                    }

                }

                $ids = collect($prices)->pluck('id')->toArray();
                FinSubscriberPrice::whereNotIn('id', $ids)
                    ->where('subscriber_id', $subscriber_id)
                    ->each(function ($price_item) {
                        $price_item->payments()->delete();
                        $price_item->delete();
                    });
            }
            if (count($prices_null) > 0) {
                foreach ($prices_null as $price) {
                    $data_pr = array(
                        'date' => Carbon::createFromFormat('Y-m-d', date('Y-m-d')),
                        'price' => $price['price'],
                        'subscriber_id' => $subscriber_id,
                    );
                    if (count($price['payments']) > 0) {
                        $amounts = collect($price['payments'])->pluck('amount')->toArray();
                        $amount_sum = 0;
                        foreach ($amounts as $amount) {
                            $amount_sum += (int) $amount;
                        }
                        if ($amount_sum >= $price['price']) {
                            $data_pr['date_end'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        }
                    }
                    $new_pr = FinSubscriberPrice::create($data_pr);

                    // payments
                    if (count($price['payments']) > 0) {
                        foreach ($price['payments'] as $pay) {
                            $data = array(
                                'date' => $pay['date'],
                                'amount' => $pay['amount'],
                                'fin_subscriber_price_id' => $new_pr->id,
                            );
                            FinSubscriberPayment::create($data);
                        }
                    }
                }
            }
        }
        else {
            FinSubscriberPrice::where('subscriber_id', $subscriber_id)
                ->each(function ($price_item) {
                    $price_item->payments()->delete();
                    $price_item->delete();
                });
        }


        $pr_db = FinSubscriberPrice::with('payments')
            ->where('subscriber_id', $subscriber_id)
            ->get();
        return self::sendResponse($pr_db->toArray(), 'Prices and payments updated successfully');
    }
}
