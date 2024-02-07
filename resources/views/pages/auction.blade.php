@extends('layouts.app')

@section('title', 'Auction')

@section('content')

<main data-id="{{$auction}}">
  <div class="container p-5">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <div id="bidResult" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 id="bidResultHeader" class="modal-title align-self-center">.</h4>
          </div>
          <div class="modal-body">
            <p id="bidResultBody" class="alert alert-danger"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
    <table class="table">
      <tbody>
        <tr>
          <td><strong>Title</strong></td>
          <td>{{$auction->title}}</td>
        </tr>
        <tr>
          <td><strong>Description</strong></td>
          <td>{{$auction->description}}</td>
        </tr>
        <tr>
          <td><strong>Time left:</strong></td>
          <td>
              <p id="timeLeft" class="text-danger"></p>
          </td>
      </tr>
      
      <script>
        @if(isset($auction->datecreated) && isset($auction->duration))
            document.addEventListener('DOMContentLoaded', function() {
                initializeCountdown("{{ $auction->datecreated }}", {{ $auction->duration }}, {{$auction->id}});
            });
        @else
            document.getElementById("timeLeft").innerText = 'Time remaining cannot be calculated.';
        @endif
    </script>
        <tr>
          <td><strong>Current bid: </strong></td>
          <td>
            <p id="currentMaxBid" class="text-success"> @if($auction->minvalue > $maxBid){{ $auction->minvalue }} @else {{ $maxBid }}@endif€</p>
          </td>
        </tr>
        <tr>
          <td><strong>Starting Price: </strong></td>
          <td>
            <p id="startingPrice" class="text-success">{{ $auction->starting_value  }}€</p>
          </td>
        </tr>
        </tr>
        <tr>
          <td><strong>State</strong></td>
          <td>{{ $auction->state->state_name ?? 'State not set' }}</td>
        </tr>
        <tr>
          <td colspan="2">
            @if (Auth::check())
              @if ($auction->isFollowed)
                  <form method="POST" action="{{ route('auction.unfollow', $auction->id) }}">
                      @csrf
                      <button id="unfollow" type="submit" class="btn btn-primary col-md-6" style="margin-top: 3px;">Unfollow</button>
                  </form>
              @else
                  <form method="POST" action="{{ route('auction.follow', $auction->id) }}">
                      @csrf
                      <button id="follow" type="submit" class="btn btn-primary col-md-6" style="margin-top: 3px;">Follow</button>
                  </form>
              @endif
              @if(Auth::user()->id == $auction->owner_id)
                <button id="edit-auction" type="submit" class="btn btn-primary">Edit the auction</button>
                <br>
                <form action="{{ route('auction.cancelAuction', $auction->id) }}" method="POST">
                    @csrf
                    <button id="cancel-auction" type="submit" class="btn btn-primary">Cancel the auction</button>
                </form>
              @endif
            @endif
          </td>
        </tr>
        <tr>
          <td>
            @if(isset($average_rating))
              <p>Average Rating of the Seller: {{ number_format($average_rating, 1) }}/5</p>
            @else
              <p>The seller has not been rated yet.</p>
            @endif
          </td>
        </tr>
        <tr>
          <td colspan="2">
            @if($biddingHistory->isNotEmpty())
            <h2>Bidding History</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Bidder</th>
                        <th>Bid Amount</th>
                        <th>Bid Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biddingHistory as $bid)
                    <tr>
                        <td>{{ $bid->bidder_name }}</td>
                        <td>{{ $bid->bidvalue }}€</td>
                        <td>{{ \Carbon\Carbon::parse($bid->biddate)->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                
            </table>
        @endif
          </td>
      </tr>
      @if(Auth::check())
        @if(Auth::user()->id != $auction->owner_id)
          <form method="POST" action="{{ route('bid.store') }}">
            @csrf
            <input type="hidden" name="auction_id" value="{{ $auction->id }}">
            <div class="form-group">
                <label for="bid_amount">Your Bid (€):</label>
                <input id="bid_amount" type="number" name="bid_amount" class="form-control" min="{{$auction->minvalue + 1}}" required>
            </div>
            <button type="submit" class="btn btn-success mt-2">Place Bid</button>
          </form>
          <form method="POST" action="{{ route('rateSeller', $auction->owner_id) }}">
            @csrf
            <label for="rating">Rate this Seller (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" title="Choose from 1-5 a rating for this seller." required>
            <button type="submit">Submit Rating</button>
        </form>
        @endif
      @endif
      </tbody>
    </table>
  </div>
</main>

@endsection