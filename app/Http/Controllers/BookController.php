<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter');
        $books = Book::when(
            $title,
            fn($query, $title) => $query->title($title)
        );
        $books = match ($filter){
            null=> $books->latest()->withReviewsCount()->withAvgRating(),
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Month(),
            'top_rated_last_month' => $books->highestRatedLastMonth(),
            'top_rated_last_6months' => $books->highestRatedLast6Month(),
            default => $books
        };

        $cacheKey = 'books:'.$filter.':'.$title;
        $books = cache()->remember($cacheKey, 3600, fn() => $books->get());
        return view('books.index',['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $cacheKey = 'books:'.$book->id;
        $book = cache()->remember($cacheKey, 3600, fn() => $book->load([
            'reviews'=> fn($query) => $query->latest()
        ]));
        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
