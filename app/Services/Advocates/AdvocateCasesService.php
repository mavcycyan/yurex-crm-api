<?php

namespace App\Services\Advocates;

use App\Models\Advocate\AdvocateCase;
use App\Models\Advocate\AdvocateCategory;
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
                $cases = AdvocateCase::where('advocate_id', $request['advocate_id'])
                    ->where('advocate_category_id', $cat->id)
                    ->get();
            } else {
                return self::sendError('Wrong advocate category slug');
            }
        } else {
            $cases = AdvocateCase::all();
        }
        return self::sendResponse($cases->toArray(), 'Advocate cases returned successfully');
    }

    public static function show($id, Request $request) {
        $case = AdvocateCase::find($id);
        $res = $case;
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
        $case->delete();

        return response()->json(['success' => true,'message' => 'Case deleted successfully.'], 200);
    }
}
