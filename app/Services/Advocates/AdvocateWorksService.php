<?php

namespace App\Services\Advocates;

use App\Models\Advocate\AdvocateWork;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AdvocateWorksService extends Service
{
    public static function index(Request $request) {
        $query = AdvocateWork::select('*');
        $input = $request->all();
        if (isset($input['case_id']) && $input['case_id'] !== '') {
            $query->where('advocate_case_id', '=', $input['case_id']);
        }
        if (count($input) > 0) {
            $query = self::filtering($input, $query);
            $query = self::sorting($input, $query);
            $query = self::pagination($input, $query);
        }
        $works = $query->get();
        $query_count = AdvocateWork::select('*');
        if (isset($input['case_id']) && $input['case_id'] !== '') {
            $query_count->where('advocate_case_id', '=', $input['case_id']);
        }
        if (count($input) > 0) {
            $query_count = self::filtering($input, $query_count);
        }
        $count = $query_count->get()->count();
        return self::sendResponse($works->toArray(), 'Advocate works returned successfully', $count);
    }

    public static function show($id, Request $request) {
        $work = AdvocateWork::find($id);
        $work->what_done = unserialize($work->what_done);
        return self::sendResponse($work, 'Advocate work returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'date' => 'required',
            'advocate_case_id' => 'required'
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }
        $work = AdvocateWork::create($input);
        return self::sendResponse($work, 'Advocate work created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $work = AdvocateWork::find($id);

        $validator = Validator::make($input, [
            'date' => 'required',
            'advocate_case_id' => 'required'
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }


        if (isset($input['what_done']) && $input['what_done'] !== '') {
            $input['what_done'] = serialize($input['what_done']);
        }

        $work->date = $input['date'];
        $work->date_end = $input['date_end'];
        $work->name = $input['name'];
        $work->what_done = $input['what_done'];
        $work->result = $input['result'];
        $work->worker = $input['worker'];
        $work->advocate_case_id = $input['advocate_case_id'];

        $work->save();

        return self::sendResponse($work, 'Advocate work updated successfully.');
    }

    public static function destroy($id) {
        $work = AdvocateWork::find($id);

        if (!$work) {
            return self::sendError('Advocate work not found.');
        }
        $work->delete();

        return response()->json(['success' => true,'message' => 'Advocate work deleted successfully.'], 200);
    }
}
