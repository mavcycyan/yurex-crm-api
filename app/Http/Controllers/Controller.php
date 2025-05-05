<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function checkAndProcessSubscrCalc()
    {

//        $previousMonth = Carbon::parse('2025-03-01')->startOfMonth();
//        \App\Services\Calculations\SubscrCalculations\SubscrCalcLastArchive::generateForMonth($previousMonth);
        $setting = \App\Models\Settings\Settings::where('key', 'subscr_calc_date')->first();
        if (!$setting) {
            $setting = \App\Models\Settings\Settings::create([
                'key' => 'subscr_calc_date',
                'value' => null,
            ]);
        }

        $previousMonth = now()->copy()->subMonth()->startOfMonth();

        if (!$setting->value || Carbon::parse($setting->value)->lt($previousMonth)) {
            \App\Services\Calculations\SubscrCalculations\SubscrCalcLastArchive::generateForMonth($previousMonth);

            $setting->value = $previousMonth->toDateTimeString();
            $setting->save();
        }
    }
}
