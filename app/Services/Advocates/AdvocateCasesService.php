<?php

namespace App\Services\Advocates;

use App\Models\Advocate\AdvocateCase;
use App\Models\Advocate\AdvocateCategory;
use App\Models\Finance\Advocate\FinAdvocateCasePayment;
use App\Models\Finance\Advocate\FinAdvocateCasePrice;
use App\Services\Finances\Advocates\FinAdvocateCasesPaymentsService;
use App\Services\Finances\Advocates\FinAdvocateCasesPricesService;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AdvocateCasesService extends Service
{
    public static function index(Request $request) {
        if (
            isset($request['advocate_id']) &&
            $request['advocate_id'] !== '' &&
            isset($request['advocate_category_slug']) &&
            $request['advocate_category_slug'] !== ''
        ) {
            $cat = AdvocateCategory::where('slug', '=', $request['advocate_category_slug'])->first();
            if ($cat !== null) {
                $query = AdvocateCase::where('advocate_id', $request['advocate_id'])
                    ->where('advocate_category_id', $cat->id);
                $input = $request->all();
                if (count($input) > 0) {
                    $query = self::pagination($input, $query);
                }
                $cases = $query->get();
                $count = AdvocateCase::where('advocate_id', $request['advocate_id'])
                    ->where('advocate_category_id', $cat->id)->get()->count();
            } else {
                return self::sendError('Wrong advocate category slug');
            }
        } else {
            $query = AdvocateCase::select('*');
            $input = $request->all();
            if (count($input) > 0) {
                $query = self::pagination($input, $query);
            }
            $cases = $query->get();
            $count = AdvocateCase::select('*')->get()->count();
        }
        return self::sendResponse($cases->toArray(), 'Advocate cases returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $case = AdvocateCase::find($id);
        $res = $case;
        $case->advocates = unserialize($case->advocates);
        if (isset($case->advocate_works)) {
            $res->advocate_works = $case->advocate_works->map(function ($item) {
                $item->what_done = unserialize($item->what_done);
                return $item;
            });;
        }
        return self::sendResponse($res, 'Advocate case "' . $case->full_name . '" returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'visit_date' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['advocates']) && $input['advocates'] !== '') {
            $input['advocates'] = serialize($input['advocates']);
        }

        $case = AdvocateCase::create($input);
        if (isset($input['fee'])) {
            $fee = ($input['fee'] !== '') ? $input['fee'] : 0;
            $for_save = array('date' => $input['visit_date'], 'price' => $fee, 'advocate_case_id' => $case->id);
            if ($input['payment'] !== '' && $input['payment'] >= $fee) {
                $for_save['date_end'] = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            }
            $price = FinAdvocateCasesPricesService::save_new($for_save);

            if (isset($input['payment']) && $price !== false) {
                $fee = ($input['payment'] !== '') ? $input['payment'] : 0;
                $payment = FinAdvocateCasesPaymentsService::save_new(array('date' => $input['visit_date'], 'amount' => $fee, 'fin_advocate_case_price_id' => $price->id));
            }
        }

        return self::sendResponse($case, 'Advocate case created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $case = AdvocateCase::find($id);

        $validator = Validator::make($input, [
            'visit_date' => 'required',
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['advocates']) && $input['advocates'] !== '') {
            $input['advocates'] = serialize($input['advocates']);
        }

        $case->visit_date = Carbon::createFromFormat('Y-m-d', $input['visit_date']);
        $case->advocates = $input['advocates'];
        $case->proc_num = $input['proc_num'];
        $case->plot = $input['plot'];
        $case->fee = $input['fee'];
        $case->result = $input['result'];
        $case->advocate_category_id = $input['advocate_category_id'];

        $case->save();

        return self::sendResponse($case, 'Advocate case "' . $case->visit_date . '" updated successfully.');
    }

    public static function destroy($id) {
        $case = AdvocateCase::find($id);

        if (!$case) {
            return self::sendError('Advocate case not found.');
        }

        $works = $case->advocate_works;
        if (count($works) > 0) {
            foreach ($works as $work) {
                $work->delete();
            }
        }
        FinAdvocateCasePrice::where('advocate_case_id', $id)
            ->each(function ($price_item) {
                $price_item->payments()->delete();
                $price_item->delete();
            });
        $case->delete();

        return response()->json(['success' => true,'message' => 'Case deleted successfully.'], 200);
    }
}
