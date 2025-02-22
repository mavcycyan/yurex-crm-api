<?php

namespace App\Services\Finances\Clients;

use App\Models\Finance\Client\FinClientWorkPayment;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FinClientWorksPaymentsService extends Service
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
            'fin_client_work_price_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date'] = Carbon::createFromFormat('Y-m-d', $input['date']);
        $payment = FinClientWorkPayment::create($input);
        return $payment;
    }
}
