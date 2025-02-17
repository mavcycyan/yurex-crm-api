<?php

namespace App\Services;

class Service
{


    public static function sendResponse($result, $message, $count_all = null) {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        if ($count_all !== null) {
            $response['all_items'] = $count_all;
        }
        return response()->json($response);
    }
    public static function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response);
    }
    public static function pagination($params, $query) {
        if (isset($params['pag_size']) && $params['pag_size'] !== null) {
            $query->limit($params['pag_size']);
        }

        if (isset($params['pag_offset']) && $params['pag_offset'] !== null) {
            $query->offset($params['pag_offset']);
        }
        return $query;
    }
    public static function sorting($params, $query) {
        $dir = 'desc';

        if (isset($params['sort_dir']) && $params['sort_dir'] !== null) {
            $dir = $params['sort_dir'];
        }
        if (isset($params['sort_field']) && $params['sort_field'] !== null) {
            $field = $params['sort_field'];
            $query->orderBy($field, $dir);
        }
        return $query;
    }
}
