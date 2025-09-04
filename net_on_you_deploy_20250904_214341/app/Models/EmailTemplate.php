<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'language',
        'subject',
        'body',
        'variables',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    /**
     * Get the admin who created this template
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Get the admin who last updated this template
     */
    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    /**
     * Get email logs for this template
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'template_name', 'name');
    }

    /**
     * Scope to get templates by language
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope to get templates by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Get available variables as a formatted string
     */
    public function getVariablesListAttribute()
    {
        if (empty($this->variables)) {
            return 'No variables';
        }
        
        return implode(', ', array_map(function($var) {
            return '{' . $var . '}';
        }, $this->variables));
    }

    /**
     * Replace variables in the template with actual values
     */
    public function replaceVariables($data)
    {
        $body = $this->body;
        $subject = $this->subject;
        
        if (!empty($this->variables)) {
            foreach ($this->variables as $variable) {
                $placeholder = '{' . $variable . '}';
                $value = $data[$variable] ?? '';
                
                $body = str_replace($placeholder, $value, $body);
                $subject = str_replace($placeholder, $value, $subject);
            }
        }
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}
