<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'author'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query,string $title): Builder
    {
        return $query->where('title', 'like', '%'.$title.'%');
    }
    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount([
            'reviews'=> fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg([
            'reviews'=> fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $min): Builder
    {
        return $query->having('reviews_count', '>=', $min);
    }

    private function dateRangeFilter(Builder $q, $from = null, $to = null){
        if ($from && !$to) {
            $q->where('created_at', '>=', $from);
        }elseif (!$from && $to) {
            $q->where('created_at', '<=', $to);
        }elseif ($from && $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(3);
    }
    public function scopePopularLast6Month(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(6), now())
            ->highestRated(now()->subMonth(6), now())
            ->minReviews(10);
    }
    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(3);
    }
    public function scopeHighestRatedLast6Month(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonth(6), now())
            ->popular(now()->subMonth(), now(6))
            ->minReviews(10);
    }
}
