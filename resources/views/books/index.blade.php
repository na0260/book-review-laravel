@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-semibold text-slate-800 mb-4">Books</h1>

    <form method="GET" action="{{ route('books.index') }}" class="flex items-center mb-4 space-x-1">
        <input type="text" name="title" placeholder="Search by title" value="{{request('title')}}" class="input h-10"/>
        <input type="hidden" name="filter" value="{{request('filter')}}"/>
        <button type="submit" class="btn h-10">Search</button>
        <a href="{{route('books.index')}}" class="btn h-10">Clear</a>
    </form>

    <div class="filter-container mb-4 flex">
        @php
            $filters = [
                '' => 'Latest',
                'popular_last_month' => 'Popular Last Month',
                'popular_last_6months' => 'Popular Last 6 Months',
                'top_rated_last_month' => 'Top Rated Last Month',
                'top_rated_last_6months' => 'Top Rated Last 6 Months',
            ];
        @endphp
        @foreach($filters as $key => $value)
            @php
                $active = request('filter') === $key;
            @endphp
            <a href="{{route('books.index', [...request()->query(),'filter' => $key])}}"
               class="{{request('filter')===$key || (request('filter')===null && $key === '')?'filter-item-active':'filter-item'}}">
                {{$value}}
            </a>

        @endforeach
    </div>

    <ul>
        @forelse($books as $book)
            <li class="mb-4">
                <div class="book-item">
                    <div
                        class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{route('books.show',$book)}}" class="book-title">{{$book->title}}</a>
                            <span class="book-author">by {{$book->author}}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                {{ number_format($book->reviews_avg_rating,1) }}
                            </div>
                            <div class="book-review-count">
                                out of {{$book->reviews_count}} {{Str::plural('review',$book->reviews_count)}}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{route('books.index')}}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>
@endsection
