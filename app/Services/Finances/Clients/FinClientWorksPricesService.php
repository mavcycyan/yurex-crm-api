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
            isset($request['client_work_id']) &&
            $request['client_work_id'] !== ''
        ) {
            $prices = FinClientWorkPrice::with('payments')
            ->where('client_work_id', $request['client_work_id'])
            ->get();
        } else {
            $prices = FinClientWorkPrice::all();
        }
        return self::sendResponse($prices->toArray(), 'Advocate cases returned successfully');
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
        $input = $request->all();
//        $validator = Validator::make($input, [
//            'client_work_id' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//            return false;
//        }
//
//        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
//        $input['date_end'] = null;
//        $payment = FinClientWorkPrice::create($input);
        return self::sendResponse([], 'Prices and payments updated successfully');
    }
}
