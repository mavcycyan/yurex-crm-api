<?php

namespace App\Http\Controllers\Files\Subscriber;

use App\Http\Controllers\Controller;
use App\Services\Files\Subscriber\FilesSubscriberWorksService;
use Illuminate\Http\Request;

class FilesSubscriberWorks extends Controller
{

    public function getFiles($workId) {
        return FilesSubscriberWorksService::getFiles($workId);
    }
    public function upload(Request $request, $workId) {
        return FilesSubscriberWorksService::upload($request, $workId);
    }
    public function destroy($work_id, $id) {
        return FilesSubscriberWorksService::destroy($work_id, $id);
    }
}
