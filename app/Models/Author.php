<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Author extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'lastName',
        'country',
        'birthDate',
    ];

    protected $casts = [
        'birthDate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }


}