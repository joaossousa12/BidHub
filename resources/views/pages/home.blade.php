@extends('layouts.app')

@section('content')
    <form action="{{ route('search', ['query' => request('query')]) }}" method="get">
        <select name="category">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->categoryname }}">{{ $category->categoryname }}</option>
            @endforeach
        </select>
        <input type="text" name="query" placeholder="Search auctions: " value="{{ request('query') }}" title="Enter keywords, item names, or apply a filter for a category to search for auctions.">
        <button type="submit">Search</button>
    </form>
    <div class="top-nav">
        <div class="nav-item"><a href="{{ url('/faq') }}">FAQ</a></div>
        <div class="nav-item"><a href="{{ url('/about-us') }}">About Us</a></div>
        <div class="nav-item"><a href="{{ url('/main-features') }}">Main Features</a></div>   

        @if (!Auth::check())
            <div class="nav-item"><a href="{{ url('auth/login') }}">Login</a></div>
        @endif
        
        @if (Auth::check())
            <div class="nav-item"><a href="{{ url('/users/' . Auth::id()) }}">View Profile</a></div>
        @endif

    </div>
    <div class="auctions-container">
        @foreach ($auctions as $auction)
            @if($auction->state->state_name == 'approved')
            <div class="auction-box">
                <div class="auction-details">
                    <h3>{{ $auction->title }}</h3>
                    <p>{{ $auction->description }}</p>
                    <div class="auction-timing">
                        <span>{{ $auction->datecreated }}</span>
                        <span>{{ $auction->duration }} days</span>
                    </div>
                    <div class="auction-bidding">
                        <span>{{ $auction->minvalue }} €</span>
                        <a href="{{ url('/auction/' . $auction->id) }}" class="bid-now">Bid Now</a>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
@endsection
