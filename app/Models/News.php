<?php

namespace App\Models;

use App\Models\Author;
use App\Models\Source;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function scopeSearch($query, $search = null)
    {
        if ($search) {
            $query->where(function($q) use ($search) {
                $search = str_replace(' ', '|', addslashes($search));
                $q->where('title', 'regexp', $search)
                    ->orWhere('content', 'regexp', $search);
            });
        }
    }
}
