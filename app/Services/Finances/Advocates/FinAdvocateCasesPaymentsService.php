<?php

namespace App\Services\Finances\Advocates;

use App\Models\Finance\Advocate\FinAdvocateCasePayment;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FinAdvocateCasesPaymentsService extends Service
{

    public static function index(Request $request) {

    }

    public static function show($id, Request $request) {

    }

    public static function store(Request $request) {

    }

    public static function update(Request $request, $subscriber) {

    }

    public static function destroy($id) {

    }

    public static function save_new($input) {
        $validator = Validator::make($input, [
            'fin_advocate_case_price_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        $input['date'] = Carbon::createFromFormat('Y-m-d H:i:s', $input['date']);
        $payment = FinAdvocateCasePayment::create($input);
        return $payment;
    }
}
