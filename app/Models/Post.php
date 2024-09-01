<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['title', 'body', 'cover_image', 'pinned', 'user_id'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Scope for fetching pinned posts first
    public function scopePinnedFirst($query)
    {
        return $query->orderBy('pinned', 'desc');
    }
   



    // public function scopeOwnedBy($query, $userId)
    // {
    //     return $query->where('user_id', $userId);
    // }

    // // Accessors
    // public function getCoverImageUrlAttribute()
    // {
    //     return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    // }

    // // Methods
    // public function restoreDeleted()
    // {
    //     return $this->restore();
    // }

    // public static function forceDeleteOlderThanDays($days)
    // {
    //     return static::onlyTrashed()
    //         ->where('deleted_at', '<', now()->subDays($days))
    //         ->forceDelete();
    // }
}
