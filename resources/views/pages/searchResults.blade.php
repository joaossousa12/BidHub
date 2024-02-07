@extends('layouts.app')

@section('content')
    <h1>Search Results for "{{ $query }}"</h1>

    @if ($auctions->isEmpty())
        <p>No auctions have that name!</p>
    @else
        <ul>
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
        </ul>
    @endif
@endsection