<?php

namespace App\Models\Files\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FilesSubscriberWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'subscriber_work_id'
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
