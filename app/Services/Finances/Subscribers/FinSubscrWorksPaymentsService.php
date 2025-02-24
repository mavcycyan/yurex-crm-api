<?php

namespace App\Services\Finances\Subscribers;

use App\Models\Finance\Client\FinClientWorkPayment;
use App\Models\Finance\Subscriber\FinSubscrWorkPayment;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FinSubscrWorksPaymentsService extends Service
{

    public static function index(Request $request) {

    }

    public static function show($id, Request $request) {

    }

    public static function store(Request $request) {

    }

    public static function update(Request $request, Subscriber $subscriber) {

    }

    public static function destroy($id) {

    }

    public static function save_new($input) {
        $validator = Validator::make($input, [
            'fin_subscr_work_price_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

//        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $payment = FinSubscrWorkPayment::create($input);
        return $payment;
    }
}
