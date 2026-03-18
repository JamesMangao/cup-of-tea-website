<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedArticle extends Model
{
    protected $fillable = [
        'user_id',
        'news_title',
        'news_description',
        'news_source',
        'news_url',
        'news_image',
        'type',
    ];

    protected $casts = [
        'news_published' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
