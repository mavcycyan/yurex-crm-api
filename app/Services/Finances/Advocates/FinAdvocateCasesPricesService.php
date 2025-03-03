<?php

namespace App\Services\Finances\Advocates;

use App\Models\Finance\Advocate\FinAdvocateCasePayment;
use App\Models\Finance\Advocate\FinAdvocateCasePrice;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FinAdvocateCasesPricesService extends Service
{

    public static function index(Request $request) {

        if (
            isset($request['parent_id']) &&
            $request['parent_id'] !== ''
        ) {
            $prices = FinAdvocateCasePrice::with('payments')
            ->select('*')
            ->where('advocate_case_id', $request['parent_id'])
            ->get()->map(function ($item) {
                    $item->deadlines = !empty($item->deadlines) ? unserialize($item->deadlines) : null;
                    return $item;
                });
        } else {
            $prices = FinAdvocateCasePrice::all()->map(function ($item) {
                $item->deadlines = !empty($item->deadlines) ? unserialize($item->deadlines) : null;
                return $item;
            });
        }
        return self::sendResponse($prices->toArray(), 'Prices and payments returned successfully');
    }

    public static function show($id, Request $request) {
        $price = FinAdvocateCasePrice::find($id);
        $res = $price;
        return self::sendResponse($res, 'Price with amount "' . $res->amount . '" returned successfully');
    }

    public static function store(Request $request) {

    }

    public static function update(Request $request, $subscriber) {

    }

    public static function destroy($id) {

    }

    public static function save_new($input) {
        $validator = Validator::make($input, [
            'advocate_case_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date_end'] = null;
        $payment = FinAdvocateCasePrice::create($input);
        return $payment;
    }

    public static function bulk_update(Request $request) {
        $input = $request->all();
        $parent_id = $input['parent_id'];

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
                    if (isset($price['name'])) {
                        $update['name'] = $price['name'];
                    }
                    if (isset($price['deadlines']) && $price['deadlines'] !== '') {
                        $update['deadlines'] = serialize($price['deadlines']);
                    }
                    FinAdvocateCasePrice::where('id', $price['id'])
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
                                $amount_currency = '';
                                $amount_currency_val = '';
                                if (isset($pay['amount_currency']) && $pay['amount_currency'] !== null && $pay['amount_currency'] !== '') {
                                    $amount_currency = $pay['amount_currency'];
                                }
                                if (isset($pay['amount_currency_val']) && $pay['amount_currency_val'] !== null && $pay['amount_currency_val'] !== '') {
                                    $amount_currency_val = $pay['amount_currency_val'];
                                }
                                $params = ['date' => $pay['date'], 'amount' => $pay['amount']];
                                if ($amount_currency !== '' && $amount_currency_val !== '') {
                                    $params['amount_currency'] = $amount_currency . ' ' . $amount_currency_val;
                                }
                                FinAdvocateCasePayment::where('id', $pay['id'])
                                    ->update($params);
                            }
                            $p_ids = collect($payments)->pluck('id')->toArray();
                            FinAdvocateCasePayment::whereNotIn('id', $p_ids)
                                ->where('fin_advocate_case_price_id', $price['id'])
                                ->delete();
                        }

                        if (count($payments_null) > 0) {
                            foreach ($payments_null as $pay) {
                                $data = array(
                                    'date' => $pay['date'],
                                    'amount' => $pay['amount'],
                                    'fin_advocate_case_price_id' => $price['id'],
                                );
                                $amount_currency = '';
                                $amount_currency_val = '';
                                if (isset($pay['amount_currency']) && $pay['amount_currency'] !== null && $pay['amount_currency'] !== '') {
                                    $amount_currency = $pay['amount_currency'];
                                }
                                if (isset($pay['amount_currency_val']) && $pay['amount_currency_val'] !== null && $pay['amount_currency_val'] !== '') {
                                    $amount_currency_val = $pay['amount_currency_val'];
                                }
                                if ($amount_currency !== '' && $amount_currency_val !== '') {
                                    $data['amount_currency'] = $amount_currency . ' ' . $amount_currency_val;
                                }
                                FinAdvocateCasePayment::create($data);
                            }
                        }
                    } else {
                        FinAdvocateCasePayment::where('fin_advocate_case_price_id', $price['id'])
                            ->delete();
                    }

                }

                $ids = collect($prices)->pluck('id')->toArray();
                FinAdvocateCasePrice::whereNotIn('id', $ids)
                    ->where('advocate_case_id', $parent_id)
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
                        'advocate_case_id' => $parent_id,
                    );
                    if (isset($price['name'])) {
                        $data_pr['name'] = $price['name'];
                    }
                    if (isset($price['deadlines']) && $price['deadlines'] !== '') {
                        $data_pr['deadlines'] = serialize($price['deadlines']);
                    }
                    $new_pr = FinAdvocateCasePrice::create($data_pr);

                    // payments
                    if (count($price['payments']) > 0) {
                        foreach ($price['payments'] as $pay) {
                            $data = array(
                                'date' => $pay['date'],
                                'amount' => $pay['amount'],
                                'fin_advocate_case_price_id' => $new_pr->id,
                            );
                            $amount_currency = '';
                            $amount_currency_val = '';
                            if (isset($pay['amount_currency']) && $pay['amount_currency'] !== null && $pay['amount_currency'] !== '') {
                                $amount_currency = $pay['amount_currency'];
                            }
                            if (isset($pay['amount_currency_val']) && $pay['amount_currency_val'] !== null && $pay['amount_currency_val'] !== '') {
                                $amount_currency_val = $pay['amount_currency_val'];
                            }
                            if ($amount_currency !== '' && $amount_currency_val !== '') {
                                $data['amount_currency'] = $amount_currency . ' ' . $amount_currency_val;
                            }
                            FinAdvocateCasePayment::create($data);
                        }
                    }
                }
            }
        }
        else {
            FinAdvocateCasePrice::where('advocate_case_id', $parent_id)
                ->each(function ($price_item) {
                    $price_item->payments()->delete();
                    $price_item->delete();
                });
        }


        $pr_db = FinAdvocateCasePrice::with('payments')
            ->where('advocate_case_id', $parent_id)
            ->get()->map(function ($item) {
                $item->deadlines = !empty($item->deadlines) ? unserialize($item->deadlines) : null;
                return $item;
            });
        return self::sendResponse($pr_db->toArray(), 'Prices and payments updated successfully');
    }
}
