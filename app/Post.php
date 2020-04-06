<?php

namespace App;

use App\Tag;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'published_at' => 'datetime',
        'published_until' => 'datetime',
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }
    public function tags () {
        return $this->belongsToMany(Tag::class);
    }
    protected $guarded = [];
}
