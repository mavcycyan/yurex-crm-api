<?php

namespace App\Services\Calculations\SubscrCalculations;

use App\Models\Calculations\SubscrCalculation;
use App\Models\Finance\Subscriber\FinSubscriberPayment;
use App\Models\Subscriber\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscrCalcLastArchive
{
    public static function generateForMonth(Carbon $month)
    {
        $currentData = self::current_data($month);
        $subscribers = $currentData['subscribers'];
        $payments = $currentData['payments'];

        if (count($subscribers) > 0) {
            foreach ($subscribers as $subscriber) {
                $result = array(
                    'subscriber_id' => $subscriber['id'],
                    'chalk' => 0,
                    'payments' => serialize(array()),
                    'date' => $month
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

                $result['chalk'] = $subscriber['fee'] - $summ;

                $query = SubscrCalculation::select('chalk')
                    ->where('date', '=', $month->copy()->subMonth()->startOfMonth())
                    ->where('subscriber_id', '=', $subscriber['id']);
                $previous = $query->get();
                if (count($previous) > 0) {
                    $result['chalk'] += $previous[0]->chalk;
                }

                if (count($pays) > 0) {
                    $result['payments'] = serialize($pays);
                }
                SubscrCalculation::create($result);
            }
        }

    }



    public static function current_data(Carbon $month)
    {
        $query = Subscriber::select('id', 'name', 'holder');
        $query->where('inactive', '=', 0);
        $subscribers = $query->get()
            ->map(function ($subscriber) {
                return [
                    'id' => $subscriber->id,
                    'name' => $subscriber->name,
                    'holder' => $subscriber->holder,
                    'fee' => $subscriber->latestFinSubscriberPrice->price ?? null,
                    'fee_id' => $subscriber->latestFinSubscriberPrice->id ?? null
                ];
            });
        unset($query);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $query = FinSubscriberPayment::select('amount', 'date', 'fin_subscriber_price_id');
        $query->whereBetween('date', [$start, $end]);
        $query->orderBy('date', 'asc');
        $payments = $query->get();

        $return = array(
            'subscribers' => $subscribers,
            'payments' => $payments
        );

        return $return;
    }


    public static function generateForInactive($id)
    {
        $month = now()->copy()->startOfMonth();


        $query = Subscriber::select('id', 'name', 'holder');
        $query->where('id', '=', $id);
        $subscribers = $query->get()
            ->map(function ($subscriber) {
                return [
                    'id' => $subscriber->id,
                    'name' => $subscriber->name,
                    'holder' => $subscriber->holder,
                    'fee' => $subscriber->latestFinSubscriberPrice->price ?? null,
                    'fee_id' => $subscriber->latestFinSubscriberPrice->id ?? null
                ];
            });
        unset($query);

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $query = FinSubscriberPayment::select('amount', 'date', 'fin_subscriber_price_id');
        $query->whereBetween('date', [$start, $end]);
        $query->orderBy('date', 'asc');
        $payments = $query->get();

        $currentData = array(
            'subscribers' => $subscribers,
            'payments' => $payments
        );

        $subscribers = $currentData['subscribers'];
        $payments = $currentData['payments'];

        if (count($subscribers) > 0) {
            foreach ($subscribers as $subscriber) {
                $result = array(
                    'subscriber_id' => $subscriber['id'],
                    'chalk' => 0,
                    'payments' => serialize(array()),
                    'date' => $month
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

                $result['chalk'] = $subscriber['fee'] - $summ;

                $query = SubscrCalculation::select('chalk')
                    ->where('date', '=', $month->copy()->subMonth()->startOfMonth())
                    ->where('subscriber_id', '=', $subscriber['id']);
                $previous = $query->get();
                if (count($previous) > 0) {
                    $result['chalk'] += $previous[0]->chalk;
                }

                if (count($pays) > 0) {
                    $result['payments'] = serialize($pays);
                }
                SubscrCalculation::create($result);
            }
        }

    }
}
