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
        return $response;
    }
    public static function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return $response;
    }
    public static function filtering($params, $query) {
        if (isset($params['size']) && $params['size'] !== null) {
            $query->limit($params['size']);
        }

        if (isset($params['offset']) && $params['offset'] !== null) {
            $query->offset($params['offset']);
        }
        return $query;
    }
}
