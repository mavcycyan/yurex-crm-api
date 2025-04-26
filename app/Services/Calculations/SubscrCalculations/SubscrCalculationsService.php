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
        $query = Subscriber::select('id', 'name', 'holder');
        $query->where('inactive', '=', 0);
        $subscribers = $query->get();
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
                    'subscriber_id' => $calc->subscriber_id,
                ];
            });
        ///

        $current_month_data = self::get_current_month_data(Carbon::today());
        return self::sendResponse(array(array('subscribers' => $subscribers), array('calculations' => $subsc_calcs), array('current_month_data' => $current_month_data)), 'Subscribers calculations returned successfully');
    }

    public static function show($id, Request $request) {
        return self::sendResponse([], 'Show');
    }

    public static function store(Request $request) {

        return self::sendResponse([], 'Store');
    }

    public static function update(Request $request, SubscrCalculation $subscriber) {

        return self::sendResponse($subscriber, 'Update');
    }

    public static function destroy($id) {
        return response()->json(['success' => true,'message' => 'Destroy'], 200);
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
