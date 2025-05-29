<?php

namespace App\Services\Calculations\SubscrCalculations;

use App\Models\Auth\User;
use App\Models\Calculations\SubscrCalculation;
use App\Models\Finance\Subscriber\FinSubscriberPayment;
use App\Models\Subscriber\Subscriber;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscrCalculationsService extends Service
{

    public static function index(Request $request) {
        $query = Subscriber::select('id', 'name', 'holder', 'inactive', 'stop_reason')
            ->orderBy('inactive')
            ->orderBy('name');
        $subscribers = $query->get()
            ->map(function ($subscriber) {
                return [
                    'id' => $subscriber->id,
                    'name' => $subscriber->name,
                    'holder' => $subscriber->holder,
                    'fee' => $subscriber->latestFinSubscriberPrice->price ?? null,
                    'inactive' => $subscriber->inactive,
                    'stop_reason' => $subscriber->stop_reason,
                ];
            });
        ///

        unset($query);
        $query = SubscrCalculation::select('*');
        $query->whereIn('subscriber_id', $subscribers->pluck('id'));
        $input = $request->all();
        $date = Carbon::today();

        if (count($input) > 0) {
            if (isset($input['date']) && $input['date'] !== '') {
                $date = Carbon::parse($input['date']);
            }
        }
        $start = $date->copy()->startOfYear()->format('Y-m-d');
        $end = $date->copy()->endOfYear()->format('Y-m-d');
        $query->whereBetween('date', [$start, $end]);
        $subsc_calcs = $query->get()
            ->map(function ($calc) {
                return [
                    'id' => $calc->id,
                    'chalk' => $calc->chalk,
                    'payments' => ($calc->payments) ? unserialize($calc->payments) : null,
                    'date' => $calc->date,
                    'subscriber_id' => (int) $calc->subscriber_id,
                ];
            });
        ///

//        $current_month_data = self::get_current_month_data(Carbon::today());
//        return self::sendResponse(array('subscribers' => $subscribers, 'calculations' => $subsc_calcs, 'current_month_data' => $current_month_data), 'Subscribers calculations returned successfully');
        return self::sendResponse(array('subscribers' => $subscribers, 'calculations' => $subsc_calcs, 'current_month_data' => array()), 'Subscribers calculations returned successfully');
    }

    public static function show($id, Request $request) {
        $subsc_calc = SubscrCalculation::find($id);
        $subsc_calc->payments = unserialize($subsc_calc->payments);
        return self::sendResponse($subsc_calc, 'Subscriber calculation returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'subscriber_id' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }
        $month = Carbon::parse($input['date']);
        $subsc_calc = SubscrCalculation::select('id')
                        ->where('subscriber_id', '=', $input['subscriber_id'])
                        ->where('date', '=', $month->copy()->startOfMonth())
                        ->first();
        if ($subsc_calc !== null) {
            return self::sendError('This subscriber (' . $input['subscriber_id'] . ') already has records for this month (' . $input['date'] . ').', []);
        }
        if (isset($input['payments'])) {
            $input['payments'] = serialize($input['payments']);
        } else {
            $input['payments'] = serialize(array());
        }
        $input['date'] = $month->copy()->startOfMonth();
        $subscriber = SubscrCalculation::create($input);

        return self::sendResponse($subscriber, 'Subscriber calculation created successfully');
    }

    public static function update(Request $request, SubscrCalculation $subscriber) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'subscriber_id' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        $subscriber->subscriber_id = $input['subscriber_id'];
        $subscriber->chalk = $input['chalk'];
        if (isset($input['payments'])) {
            $subscriber->payments = serialize($input['payments']);
        } else {
            $subscriber->payments = serialize(array());
        }

        $subscriber->save();

        return self::sendResponse($subscriber, 'Subscriber calculation updated successfully');
    }

    public static function get_current_month_data(Carbon $month) {
        $currentData = SubscrCalcLastArchive::current_data($month);
        $subscribers = $currentData['subscribers'];
        $payments = $currentData['payments'];
        $result = array();
        if (count($subscribers) > 0) {
            foreach ($subscribers as $subscriber) {
                $res = array(
                    'subscriber_id' => $subscriber['id'],
                    'chalk' => '',
                    'payments' => array(),
                    'date' => $month->format('Y-m-d')
                );
                $summ = 0;
                $pays = array();
                if (count($payments) > 0) {
                    foreach ($payments as $payment) {
                        if ($payment->fin_subscriber_price_id == $subscriber['fee_id']) {
                            $pays[] = array('date' => $payment->date, 'amount' => $payment->amount);
                            $summ += (double) $payment->amount;
                        }
                    }
                }

                $chalk = (double) $subscriber['fee'] - (double) $summ;

                $query = SubscrCalculation::select('chalk')
                    ->where('date', '=', $month->copy()->subMonth()->startOfMonth())
                    ->where('subscriber_id', '=', $subscriber['id']);
                $previous = $query->get();
                if (count($previous) > 0) {
                    $chalk += (double) $previous[0]->chalk;
                }
                $res['chalk'] = $chalk . '';

                if (count($pays) > 0) {
                    $res['payments'] = $pays;
                }

                $result[] = $res;
            }
        }
        return $result;
    }
}
