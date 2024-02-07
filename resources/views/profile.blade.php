@extends('layouts.app')

@section('content')
    <div class="container">
    @if(Auth::id() == $user->id && count($notifications) > 0)
    <div class="notifications-container">
        <h3>Notifications</h3>
        <ul class="notification-list">
            @foreach($notifications as $notification)
                @if(!$notification->viewed)
                    <li class="notification-item">
                        <input type="checkbox" class="notification-checkbox" data-notification-id="{{ $notification->id }}">
                        {{ $notification->information }}
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.notification-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const notificationId = this.getAttribute('data-notification-id');
                updateNotificationStatus(notificationId);
                this.disabled = true;
            });
        });

        function updateNotificationStatus(notificationId) {

            const csrfToken = '{{ csrf_token() }}';

            fetch('/update-notification-status/' + notificationId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const listItem = document.querySelector(`.notification-checkbox[data-notification-id="${notificationId}"]`).closest('li');
                listItem.style.display = 'none';
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
        });
    </script>
        @if(Auth::id() != $user->id)
            <div class="user-profile">
                    
                    <div class="user-details">
                        <br>
                        <h2>{{ $user->username }}'s profile</h2>
                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/defaultAvatar.jpg') }}" width="200" height="200" class="profile-img" alt="{{ $user->username }} photo">
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Address:</strong> {{ $user->address }}</p>
                        <p><strong>Postal Code:</strong> {{ $user->postalcode }}</p>
                        <p><strong>Phone Number:</strong> {{ $user->phonenumber }}</p>
                    </div>
            </div>
            @if(Auth::user())
                @if(!Auth::user()->is_admin)
                    <div class="alert alert-danger">This profile is not yours, you cannot edit it!</div>
                @endif
            @endif
        @endif

        <h2>Bidding Auctions:</h2>
        <ul class="listt">
        @if (count($biddingAuctions) > 0)
            @foreach ($biddingAuctions as $auction)
                @if($auction->state_id != 4)
                    <li>
                        <p>Auction Title: {{ $auction->title }}</p>
                        <p>Description: {{ $auction->description }}</p>
                        <p>Duration: {{ $auction->duration}} min</p>
                    </li>
                @endif
            @endforeach
        @else
            <li>
                <p>{{ $user->username }} has no active bids.</p>
            </li>
        @endif
        </ul>

        <h2>Owned Auctions:</h2>
        <ul class="listt">
        @if (count($ownedAuctions) > 0)
            @foreach ($ownedAuctions as $auction)
                @if($auction->state_id != 4)
                    <li>
                        <p>Auction Title: {{ $auction->title }}</p>
                        <p>Description: {{ $auction->description }}</p>
                        <p>Duration: {{ $auction->duration}} min</p>
                    </li>
                @endif
            @endforeach
        @else
            <li>
                <p>{{ $user->username }} has no active auctions.</p>
            </li>
        @endif
        </ul>

        @if(Auth::id() == $user->id)
            <a href="{{ route('createAuctionView') }}" class="createAuctionButton">Create New Auction</a>
        @endif

        @if(Auth::id() == $user->id)
            <div class="user-profile">
                
                <div class="user-details">
                    <br>
                    <h2>Hello {{ $user->username }},</h2>
                    <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/defaultAvatar.jpg') }}" width="200" height="200" class="profile-img" alt="{{ $user->username }} photo">
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Address:</strong> {{ $user->address }}</p>
                    <p><strong>Postal Code:</strong> {{ $user->postalcode }}</p>
                    <p><strong>Phone Number:</strong> {{ $user->phonenumber }}</p>
                    <p><strong>Credits:</strong> {{ $user->credit }} €</p>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        @endif
        
        @if(Auth::user())
            @if(Auth::user()->is_admin && Auth::id() != $user->id)
                <button id="edit-user" type="submit" class="btn btn-primary user{{ $user->id }}">Edit {{$user->username}}'s profile</button>
            @else
                @if(Auth::id() == $user->id)
                    <button id="edit-user" type="submit" class="btn btn-primary user{{ $user->id }}">Edit your profile</button>
                @endif
            @endif
                
            <br>

            @if(Auth::id() == $user->id)
                <form action="{{ route('users.delete') }}" method="post">
                    @csrf
                    <button id="delete_user" type="submit" class="btn btn-primary">Delete Account</button>
                </form>
            @endif

            @if(Auth::id() == $user->id || Auth::user()->is_admin)
                <form method="POST" action="{{ url('users/addCredit/' . $user->id) }}">
                    @csrf
                    <label for="credit">Add Credits:</label>
                    <input type="number" name="credit" id="credit" min="1" required>
                    <button type="submit">Add</button>
                </form>
            @endif
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    @endif

    @if(Auth::id() == $user->id)
        <h2>Following Auctions:</h2>
        <ul>
            @if (count($followingAuctions) > 0)
                @foreach ($followingAuctions as $auction)
                    @if($auction->state_id != 4)
                        <li>
                            <p>Auction Title: {{ $auction->title }}</p>
                            <p>Description: {{ $auction->description }}</p>
                            <p>Duration: {{ $auction->duration }}</p>
                        </li>
                    @endif
                @endforeach
            @else
                <li>You are not following any auctions.</li>
            @endif
        </ul>
    @endif

</div>
@endsection
