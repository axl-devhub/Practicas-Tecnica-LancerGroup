<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'published_at',
        'edition',
        'cover_url',
    ];

    protected $casts = [
        'published_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
}