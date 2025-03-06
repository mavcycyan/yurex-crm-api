<?php

namespace App\Models\Files\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FilesClientWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'client_work_id'
    ];

    public function getFileUrlAttribute()
    {
        return url('storage/' . $this->file_path);
    }

    // Подія видалення файлу
    protected static function booted()
    {
        static::deleting(function ($workFile) {
            Storage::disk('public')->delete($workFile->file_path);
        });
    }
}
