<?php

namespace App\Http\Controllers\Files\Clients;

use App\Http\Controllers\Controller;
use App\Services\Files\Clients\FilesClientWorksService;
use Illuminate\Http\Request;

class FilesClientWorks extends Controller
{

    public function getFiles($workId) {
        return FilesClientWorksService::getFiles($workId);
    }
    public function upload(Request $request, $workId) {
        return FilesClientWorksService::upload($request, $workId);
    }
    public function destroy($work_id, $id) {
        return FilesClientWorksService::destroy($work_id, $id);
    }
}
