<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Magazine extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'cover_image_path',
        'category',
        'language_code',
        'status',
        'uploaded_by_admin_id',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the admin who uploaded this magazine
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by_admin_id');
    }

    /**
     * Get magazine entitlements
     */
    public function entitlements()
    {
        return $this->hasMany(MagazineEntitlement::class);
    }

    /**
     * Get magazine versions
     */
    public function versions()
    {
        return $this->hasMany(MagazineVersion::class);
    }

    /**
     * Get magazine views/analytics
     */
    public function views()
    {
        return $this->hasMany(MagazineView::class);
    }

    /**
     * Check if magazine is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if magazine is published
     */
    public function isPublished(): bool
    {
        return !is_null($this->published_at);
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get download URL
     */
    public function getDownloadUrl(): string
    {
        return route('admin.magazines.download', $this->id);
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrl(): ?string
    {
        if (!$this->cover_image_path) {
            return null;
        }
        return Storage::disk('public')->url($this->cover_image_path);
    }

    /**
     * Get cover image or default placeholder
     */
    public function getCoverImageUrlOrDefault(): string
    {
        return $this->getCoverImageUrl() ?? asset('images/magazine-placeholder.jpg');
    }

    /**
     * Scope for active magazines
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for published magazines
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope for magazines by language
     */
    public function scopeByLanguage($query, string $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }

    /**
     * Scope for magazines by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get available categories
     */
    public static function getAvailableCategories(): array
    {
        return self::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages(): array
    {
        return self::distinct()
            ->pluck('language_code')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Record a view action
     */
    public function recordView(int $userId, string $action = 'viewed'): void
    {
        $this->views()->create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'device' => $this->getDeviceType(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone|iPad|Windows Phone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }
}
