<?php

namespace App\Http\Controllers\Files\Advocate;

use App\Http\Controllers\Controller;
use App\Services\Files\Advocate\FilesAdvocateWorksService;
use Illuminate\Http\Request;

class FilesAdvocateWorks extends Controller
{

    public function getFiles($workId) {
        return FilesAdvocateWorksService::getFiles($workId);
    }
    public function upload(Request $request, $workId) {
        return FilesAdvocateWorksService::upload($request, $workId);
    }
    public function destroy($work_id, $id) {
        return FilesAdvocateWorksService::destroy($work_id, $id);
    }
}
