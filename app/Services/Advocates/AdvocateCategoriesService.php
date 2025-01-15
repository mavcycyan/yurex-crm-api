<?php

namespace App\Services\Advocates;

use App\Models\Advocate\AdvocateCategory;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvocateCategoriesService extends Service
{
    public static function index(Request $request) {
        $categories = AdvocateCategory::all();
        return self::sendResponse($categories->toArray(), 'Advocate categories returned successfully');
    }

    public static function byslug(Request $request) {
        $input = $request->all();
        if ($input && isset($input['slug']) && $input['slug'] !== '') {
            $category = AdvocateCategory::where('slug', '=', $input['slug'])->first();
            return self::sendResponse($category, 'Advocate category "' . $category->name . '" returned successfully');
        }
        return self::sendError('Validation Error.', ['Field \'slug\' not sent or is empty.']);
    }

    public static function show($id, Request $request) {
        $category = AdvocateCategory::find($id);
        $res = $category;
        $res->advocate_cases = $category->advocate_cases;
        return self::sendResponse($res, 'Advocate category "' . $category->name . '" returned successfully');
    }

    public static function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        $category = AdvocateCategory::create($input);
        return self::sendResponse($category, 'Advocate category created successfully');
    }

    public static function update(Request $request, string $id) {
        $input = $request->all();
        $category = AdvocateCategory::find($id);

        $validator = Validator::make($input, [
            'slug' => 'required',
        ]);

        if($validator->fails()){
            return self::sendError('Validation Error.', $validator->errors());
        }

        $category->name = $input['name'];
        $category->slug = $input['slug'];

        $category->save();

        return self::sendResponse($category, 'Advocate category "' . $category->name . '" updated successfully.');
    }

    public static function destroy($id) {
        $category = AdvocateCategory::find($id);

        if (!$category) {
            return self::sendError('Advocate category not found.');
        }
        $category->delete();

        return response()->json(['success' => true,'message' => 'Advocate category deleted successfully.'], 200);
    }
}
