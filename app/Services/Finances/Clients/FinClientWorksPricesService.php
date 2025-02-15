<?php

namespace App\Services\Finances\Clients;

use App\Models\Finance\Client\FinClientWorkPayment;
use App\Models\Finance\Client\FinClientWorkPrice;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FinClientWorksPricesService extends Service
{

    public static function index(Request $request) {

        if (
            isset($request['parent_id']) &&
            $request['parent_id'] !== ''
        ) {
            $prices = FinClientWorkPrice::with('payments')
            ->where('client_work_id', $request['parent_id'])
            ->get();
        } else {
            $prices = FinClientWorkPrice::all();
        }
        return self::sendResponse($prices->toArray(), 'Prices and payments returned successfully');
    }

    public static function show($id, Request $request) {
        $price = FinClientWorkPrice::find($id);
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
            'client_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $input['date_end'] = null;
        $payment = FinClientWorkPrice::create($input);
        return $payment;
    }

    public static function bulk_update(Request $request) {
        /*
{
    "prices": [
        {
            "id": 2,
            "price": "1500.00",
            "payments": [
                {
                    "id": 1,
                    "amount": "500.00",
                    "date": "2025-01-28"
                }
            ]
        },
        {
            "id": null,
            "price": "2000",
            "payments": [
                {
                    "id": null,
                    "amount": "1000",
                    "date": "2025-02-10"
                },
                {
                    "id": null,
                    "amount": "500",
                    "date": "2025-02-12"
                }
            ]
        }
    ],
    "parent_id": 9
}
         * */
        $input = $request->all();
        $client_work_id = $input['parent_id'];

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
                    FinClientWorkPrice::where('id', $price['id'])
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
                                FinClientWorkPayment::where('id', $pay['id'])
                                    ->update(['date' => $pay['date'], 'amount' => $pay['amount']]);
                            }
                            $p_ids = collect($payments)->pluck('id')->toArray();
                            FinClientWorkPayment::whereNotIn('id', $p_ids)
                                ->where('fin_client_work_price_id', $price['id'])
                                ->delete();
                        }

                        if (count($payments_null) > 0) {
                            foreach ($payments_null as $pay) {
                                $data = array(
                                    'date' => $pay['date'],
                                    'amount' => $pay['amount'],
                                    'fin_client_work_price_id' => $price['id'],
                                );
                                FinClientWorkPayment::create($data);
                            }
                        }
                    } else {
                        FinClientWorkPayment::where('fin_client_work_price_id', $price['id'])
                            ->delete();
                    }

                }

                $ids = collect($prices)->pluck('id')->toArray();
                FinClientWorkPrice::whereNotIn('id', $ids)
                    ->where('client_work_id', $client_work_id)
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
                        'client_work_id' => $client_work_id,
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
                    $new_pr = FinClientWorkPrice::create($data_pr);

                    // payments
                    if (count($price['payments']) > 0) {
                        foreach ($price['payments'] as $pay) {
                            $data = array(
                                'date' => $pay['date'],
                                'amount' => $pay['amount'],
                                'fin_client_work_price_id' => $new_pr->id,
                            );
                            FinClientWorkPayment::create($data);
                        }
                    }
                }
            }
        }
        else {
            FinClientWorkPrice::where('client_work_id', $client_work_id)
                ->each(function ($price_item) {
                    $price_item->payments()->delete();
                    $price_item->delete();
                });
        }


        $pr_db = FinClientWorkPrice::with('payments')
            ->where('client_work_id', $client_work_id)
            ->get();
        return self::sendResponse($pr_db->toArray(), 'Prices and payments updated successfully');
    }
}
