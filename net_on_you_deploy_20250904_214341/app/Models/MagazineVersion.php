<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagazineVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'magazine_id',
        'file_path',
        'version',
        'notes',
        'uploaded_by_admin_id',
    ];

    /**
     * Get the magazine this version belongs to
     */
    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    /**
     * Get the admin who uploaded this version
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by_admin_id');
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedFileSize(): string
    {
        if (!file_exists(storage_path('app/public/' . $this->file_path))) {
            return '0 B';
        }

        $bytes = filesize(storage_path('app/public/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get download URL for this version
     */
    public function getDownloadUrl(): string
    {
        return route('admin.magazine-versions.download', $this->id);
    }
}
