<?php

namespace App\Models\Files\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FilesAdvocateWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'advocate_work_id'
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
