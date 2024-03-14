<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['book_id', 'review', 'rating'];
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
