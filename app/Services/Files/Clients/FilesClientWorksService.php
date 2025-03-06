<?php

namespace App\Services\Files\Clients;

use App\Models\Files\Client\FilesClientWork;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FilesClientWorksService extends Service
{

    public static function getFiles($work_id)
    {
        $files = FilesClientWork::where('client_work_id', $work_id)->get()->map(function ($file) {
            return [
                'id'        => $file->id,
                'file_name' => $file->file_name,
                'file_url'  => $file->file_url,
            ];
        });

        return self::sendResponse($files->toArray(), 'Files returned successfully');
    }

    public static function upload(Request $request, $work_id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'file' => 'required|file|max:20480',
        ]);

        if ($validator->fails()) {
            return self::sendError('Validation Error.', $validator->errors());
        }

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('client_work_files', 'public');

        $workFile = FilesClientWork::create([
            'client_work_id'   => $work_id,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
        ]);


        $files = FilesClientWork::where('client_work_id', $work_id)->get()->map(function ($file) {
            return [
                'id'        => $file->id,
                'file_name' => $file->file_name,
                'file_url'  => $file->file_url,
            ];
        });

        return self::sendResponse($files->toArray(), 'Файл ' . $workFile->file_name . ' додано успішно');
    }

    public static function destroy($work_id, $id)
    {
        $file = FilesClientWork::find($id);
        $file_name = $file->file_name;

        if (!$file) {
            return self::sendError('Файл не знайдено.');
        }

        $file->delete();

        $files = FilesClientWork::where('client_work_id', $work_id)->get()->map(function ($file) {
            return [
                'id'        => $file->id,
                'file_name' => $file->file_name,
                'file_url'  => $file->file_url,
            ];
        });

        return self::sendResponse($files->toArray(), 'Файл "' . $file_name . '" видалено успішно.');
    }
}
