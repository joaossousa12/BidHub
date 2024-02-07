@extends('layouts.adminDash')

@section('content')
<div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1 active" id="dashboardOption">
                        <img src="{{ asset('images/dashboard.png') }}" class="nav-img" alt="dashboard">
                        <h3> Dashboard</h3>
                    </div>
 
                    <div class="nav-option option2" id="manageUsersOption">
                        <img src="{{ asset('images/manageUsers.jpg') }}" class="nav-img" alt="manageUsers">
                        <h3> Manage Users </h3>
                    </div>
 
                    <div class="nav-option option3" id="manageCategoriesOption">
                        <img src="{{ asset('images/categoriesmng.png') }}" class="nav-img" alt="categoriesmng">
                        <h3> Manage Categories </h3>
                    </div>
                    
                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="button" id="logout">
                            <div class="nav-option logout">
                                <img src="{{ asset('images/logout.png') }}" class="nav-img" alt="logout">
                                <h3>Logout</h3>
                            </div>
                        </button>
                    </form>
                </div>
            </nav>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mainContainer = document.querySelector('.main');
                
                const navOptions = {
                    dashboardOption: '<div class="main"> <div class="box-container"> <div class="box box1"> <div class="text"> <h2 class="topic-heading">{{ count($auctions) }}</h2> <h2 class="topic">Auctions</h2> </div> <img src="{{ asset('images/auctionicn.png') }}" alt="Auctions"> </div> <div class="box box2"> <div class="text"> <h2 class="topic-heading">{{ count($bids) }}</h2> <h2 class="topic">Bids</h2> </div> <img src="{{ asset('images/bidicn.png') }}" alt="bids"> </div> <div class="box box3"> <div class="text"> <h2 class="topic-heading">{{ count($users) }}</h2> <h2 class="topic">Users</h2> </div> <img src= "{{ asset('images/usersicn.png') }}" alt="users"> </div> <div class="box box4"> <div class="text"> <h2 class="topic-heading">{{ count($categories) }}</h2> <h2 class="topic">Categories</h2> </div> <img src="{{ asset('images/categoriesicn.png') }}" alt="categories"> </div> </div> <div class="report-container"> <div class="report-header"> <h1 class="recent-Auctions">Recent Auctions</h1> <button class="view" id="view-all-button">View All</button> </div> <div class="report-body" id="latest-auctions"> <div class="report-topic-heading"> <h3 class="t-op">Auction</h3> <h3 class="t-op">Bids</h3> <h3 class="t-op">Creator</h3> <h3 class="t-op">Status</h3> </div> <div class="items"> @foreach($auctions->sortByDesc('id')->take(9) as $auction)@if($auction->state_id != 4) <div class="item1"> <h3 class="t-op-nextlvl">{{ $auction->title }}</h3> <h3 class="t-op-nextlvl">{{ $bids->where('auction_id', $auction->id)->count() }}</h3> <h3 class="t-op-nextlvl">{{ $users->firstWhere('id', $auction->owner_id)->username }}</h3> <!-- Manage Auctions --> <form class="t-op-nextlvl label-tag {{ $auction->state->state_name }}" action="{{ route('adminDashboard.post') }}" method="POST"> @csrf <input type="hidden" name="auction_id" value="{{ $auction->id }}"> <select name="status" onchange="this.form.submit()"> <option value="approved" {{ $auction->state->state_name == 'approved' ? 'selected' : '' }}>approved</option> <option value="waiting" {{ $auction->state->state_name == 'waiting' ? 'selected' : '' }}>waiting</option> <option value="denied" {{ $auction->state->state_name == 'denied' ? 'selected' : '' }}>denied</option><option value="cancelled" {{ $auction->state->state_name == 'cancelled' ? 'selected' : '' }}>cancelled</option> </select> </form> </div>@endif @endforeach </div> </div> <div class="report-body" id="all-auctions" style="display: none;"> <div class="report-topic-heading"> <h3 class="t-op">Auction</h3> <h3 class="t-op">Bids</h3> <h3 class="t-op">Creator</h3> <h3 class="t-op">Status</h3> </div> <div class="items"> @foreach($auctions->sortByDesc('id') as $auction)@if($auction->state_id != 4) <div class="item1"> <h3 class="t-op-nextlvl">{{ $auction->title }}</h3> <h3 class="t-op-nextlvl">{{ $bids->where('auction_id', $auction->id)->count() }}</h3> <h3 class="t-op-nextlvl">{{ $users->firstWhere('id', $auction->owner_id)->username }}</h3> <!-- Manage Auctions --> <form class="t-op-nextlvl label-tag {{ $auction->state->state_name }}" action="{{ route('adminDashboard.post') }}" method="POST"> @csrf <input type="hidden" name="auction_id" value="{{ $auction->id }}"> <select name="status" onchange="this.form.submit()"> <option value="approved" {{ $auction->state->state_name == 'approved' ? 'selected' : '' }}>approved</option> <option value="waiting" {{ $auction->state->state_name == 'waiting' ? 'selected' : '' }}>waiting</option> <option value="denied" {{ $auction->state->state_name == 'denied' ? 'selected' : '' }}>denied</option><option value="cancelled" {{ $auction->state->state_name == 'cancelled' ? 'selected' : '' }}>cancelled</option> </select> </form> </div>@endif @endforeach </div> </div> </div> </div>',
                    manageUsersOption: '<div class="report-container"> <div class="report-header"> <h1 class="recent-Users">Users</h1> <button class="view" id="view-all-button2">View All</button> </div> <div class="report-body" id="latest-users"> <div class="report-topic-heading"> <h3 class="t-op">Username</h3> <h3 class="t-op">E-Mail</h3> <h3 class="t-op ban-cell">Ban</h3> <h3 class="t-op">Delete Account</h3> </div> <div class="items"> @foreach($users->sortByDesc('id')->take(9) as $user) @if(!$user->is_admin && !$user->is_deleted) <div class="item1"> <h3 class="t-op-nextlvl">{{ $user->username }}</h3> <h3 class="t-op-nextlvl">{{ $user->email }}</h3> <form action="{{ $user->is_banned ? route('admin.unbanUser', $user->id) : route('admin.banUser', $user->id) }}" class="label-tag t-op-nextlvl" method="POST"> @csrf <button type="submit" class="{{ $user->is_banned ? 'unban-button' : 'ban-button' }}" onclick="return confirm(\'Are you sure?\')"> {{ $user->is_banned ? 'Unban' : 'Ban' }} </button> </form> <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="delete-user-form label-tag t-op-nextlvl"> @csrf <button type="submit" class="delete-button" onclick="return confirm(\'Are you sure you want to delete this user?\')"> Delete </button> </form> </div> @endif @endforeach </div> </div> <div class="report-body" id="all-users" style="display: none;"> <div class="report-topic-heading"> <h3 class="t-op">Username</h3> <h3 class="t-op">E-Mail</h3> <h3 class="t-op ban-cell">Ban</h3> <h3 class="t-op">Delete Account</h3> </div> <div class="items"> @foreach($users->sortByDesc('id') as $user) @if(!$user->is_admin && !$user->is_deleted) <div class="item1"> <h3 class="t-op-nextlvl">{{ $user->username }}</h3> <h3 class="t-op-nextlvl">{{ $user->email }}</h3> <form action="{{ $user->is_banned ? route('admin.unbanUser', $user->id) : route('admin.banUser', $user->id) }}" class="label-tag t-op-nextlvl" method="POST"> @csrf <button type="submit" class="{{ $user->is_banned ? 'unban-button' : 'ban-button' }}" onclick="return confirm(\'Are you sure?\')"> {{ $user->is_banned ? 'Unban' : 'Ban' }} </button> </form> <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="delete-user-form label-tag t-op-nextlvl"> @csrf <button type="submit" class="delete-button" onclick="return confirm(\'Are you sure you want to delete this user?\')"> Delete </button> </form> </div> @endif @endforeach </div> </div> </div> <div class="create-user-container"> <h2>Create New User</h2> <form class="createUserForm" action="{{ route('admin.createUser') }}" method="POST"> @csrf <input class="createUserInput" id="username" type="text" name="username" placeholder="Name" required autofocus /> @if ($errors->has('username')) <span class="error"> {{ $errors->first('username') }} </span> @endif <input class="createUserInput" id="address" type="text" name="address" placeholder="Address" required/> @if ($errors->has('address')) <span class="error"> {{ $errors->first('address') }} </span> @endif <input class="createUserInput" id="postalcode" type="text" name="postalcode" placeholder="Postal Code" required/> @if ($errors->has('postalcode')) <span class="error"> {{ $errors->first('postalcode') }} </span> @endif <input class="createUserInput" id="phonenumber" type="text" name="phonenumber" placeholder="Phone Number" required/> @if ($errors->has('phonenumber')) <span class="error"> {{ $errors->first('phonenumber') }} </span> @endif <input class="createUserInput" id="email" type="email" name="email" placeholder="E-Mail Address" required/> @if ($errors->has('email')) <span class="error"> {{ $errors->first('email') }} </span> @endif <input class="createUserInput" id="password" type="password" name="password" placeholder="Password" required/> @if ($errors->has('password')) <span class="error"> {{ $errors->first('password') }} </span> @endif <input class="createUserInput" id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" required/> <button class="createUserButton" type="submit">Create User</button> </form> </div></div>',
                    manageCategoriesOption: `<div class="report-container">
                                                <div class="report-header">
                                                    <h1 class="recent-Auctions">Recent Auctions</h1>
                                                    <button class="view" id="view-all-button3">View All</button> 
                                                </div>

                                                <div class="report-body" id="latest-auctions1">
                                                    <div class="report-topic-heading">
                                                        <h3 class="t-op">Auction</h3>
                                                        <h3 class="t-op">Category</h3>
                                                    </div>

                                                    <div class="items">
                                                    @foreach($auctions->sortByDesc('id')->take(9) as $auction)
                                                        @if($auction->state_id != 4)
                                                            <div class="item1">
                                                                <h3 class="t-op-nextlvl">{{ $auction->title }}</h3>
                                                                <form class="t-op-nextlvl label-tag" action="{{ route('admin.updateAuctionCategory') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="auction_id" value="{{ $auction->id }}">
                                                                    <select name="category" onchange="this.form.submit()">
                                                                        @foreach($categories as $category)
                                                                            <option value="{{ $category->categoryname }}" 
                                                                                {{ $auction->categories->contains('categoryname', $category->categoryname) ? 'selected' : '' }}>
                                                                                {{ $category->categoryname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    </div>
                                                </div>

                                                <div class="report-body" id="all-auctions1" style="display: none;">
                                                    <div class="report-topic-heading">
                                                        <h3 class="t-op">Auction</h3>
                                                        <h3 class="t-op">Category</h3>
                                                    </div>
                                                    <div class="items">
                                                        @foreach($auctions->sortByDesc('id') as $auction)
                                                            @if($auction->state_id != 4)
                                                                <div class="item1">
                                                                    <h3 class="t-op-nextlvl">{{ $auction->title }}</h3>
                                                                    <form class="t-op-nextlvl label-tag" action="{{ route('admin.updateAuctionCategory') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="auction_id" value="{{ $auction->id }}">
                                                                        <select name="category" onchange="this.form.submit()">
                                                                            @foreach($categories as $category)
                                                                                <option value="{{ $category->categoryname }}" 
                                                                                    {{ $auction->categories->contains('categoryname', $category->categoryname) ? 'selected' : '' }}>
                                                                                    {{ $category->categoryname }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="create-category-container">
                                                    <h2>Create New Category</h2>
                                                    <form class="createCategoryForm" action="{{ route('admin.storeCategory') }}" method="POST">
                                                        @csrf
                                                        <input class="createCategoryInput" type="text" name="categoryname" placeholder="Enter category name" required>
                                                        <button class="createCategoryButton" type="submit">Create Category</button>
                                                    </form>
                                            </div>
                                            `,
                };

                function updateMainContent(content) {
                    mainContainer.innerHTML = content;
                    attachViewAllEventListener();
                }

                updateMainContent(navOptions.dashboardOption);

                function attachViewAllEventListener() {
                    const viewAllButton = document.getElementById('view-all-button');
                    if (viewAllButton) {
                        viewAllButton.addEventListener('click', function() {
                            var latestAuctions = document.getElementById('latest-auctions');
                            var allAuctions = document.getElementById('all-auctions');

                            if (allAuctions.style.display === 'none') {
                                allAuctions.style.display = 'block';
                                latestAuctions.style.display = 'none';
                                this.textContent = 'View Less';
                            } else {
                                allAuctions.style.display = 'none';
                                latestAuctions.style.display = 'block';
                                this.textContent = 'View All';
                            }
                        });
                    }

                    const viewAllButton2 = document.getElementById('view-all-button2');
                    if (viewAllButton2) {
                        viewAllButton2.addEventListener('click', function() {
                            var latestUsers = document.getElementById('latest-users');
                            var allUsers = document.getElementById('all-users');

                            if (allUsers.style.display === 'none') {
                                allUsers.style.display = 'block';
                                latestUsers.style.display = 'none';
                                this.textContent = 'View Less';
                            } else {
                                allUsers.style.display = 'none';
                                latestUsers.style.display = 'block';
                                this.textContent = 'View All';
                            }
                        });
                    }

                    const viewAllButton3 = document.getElementById('view-all-button3');
                    if (viewAllButton3) {
                        viewAllButton3.addEventListener('click', function() {
                            var latestUsers = document.getElementById('latest-auctions1');
                            var allUsers = document.getElementById('all-auctions1');

                            if (allUsers.style.display === 'none') {
                                allUsers.style.display = 'block';
                                latestUsers.style.display = 'none';
                                this.textContent = 'View Less';
                            } else {
                                allUsers.style.display = 'none';
                                latestUsers.style.display = 'block';
                                this.textContent = 'View All';
                            }
                        });
                    }
                }



                Object.keys(navOptions).forEach(id => {
                    const navOption = document.getElementById(id);
                    navOption.addEventListener('click', function() {
                        updateMainContent(navOptions[id]);
                        highlightActiveOption(id);
                    });
                });

                function highlightActiveOption(activeId) {
                    Object.keys(navOptions).forEach(id => {
                        const element = document.getElementById(id);
                        if (id === activeId) {
                            element.classList.add('active');
                        } else {
                            element.classList.remove('active');
                        }
                    });
                }
            });
        </script>
        <div class="main">
            <div class="box-container">
 
                <div class="box box1">
                    <div class="text">
                        <h2 class="topic-heading">{{ count($auctions) }}</h2>
                        <h2 class="topic">Auctions</h2>
                    </div>
 
                    <img src="{{ asset('images/auctionicn.png') }}" alt="Auctions">
                </div>
 
                <div class="box box2">
                    <div class="text">
                        <h2 class="topic-heading">{{ count($bids) }}</h2>
                        <h2 class="topic">Bids</h2>
                    </div>
 
                    <img src="{{ asset('images/bidicn.png') }}" alt="bids">
                </div>
 
                <div class="box box3">
                    <div class="text">
                        <h2 class="topic-heading">{{ count($users) }}</h2>
                        <h2 class="topic">Users</h2>
                    </div>
 
                    <img src= "{{ asset('images/usersicn.png') }}" alt="users">
                </div>
 
                <div class="box box4">
                    <div class="text">
                        <h2 class="topic-heading">{{ count($categories) }}</h2>
                        <h2 class="topic">Categories</h2>
                    </div>
 
                    <img src="{{ asset('images/categoriesicn.png') }}" alt="categories">
                </div>
            </div>
            
            
            <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Auctions">Recent Auctions</h1>
                    <button class="view" id="view-all-button">View All</button> 
                </div>
 
                <div class="report-body" id="latest-auctions">
                    <div class="report-topic-heading">
                        <h3 class="t-op">Auction</h3>
                        <h3 class="t-op">Bids</h3>
                        <h3 class="t-op">Creator</h3>
                        <h3 class="t-op">Status</h3>
                    </div>

                    <div class="items">
                        @foreach($auctions->sortByDesc('id')->take(9) as $auction)
                            @if($auction->state_id != 4)
                                <div class="item1">
                                    <h3 class="t-op-nextlvl">{{ $auction->title }}</h3>
                                    <h3 class="t-op-nextlvl">{{ $bids->where('auction_id', $auction->id)->count() }}</h3>
                                    <h3 class="t-op-nextlvl">{{ $users->firstWhere('id', $auction->owner_id)->username }}</h3> 
                                    <form class="t-op-nextlvl label-tag {{ $auction->state->state_name }}" action="{{ route('adminDashboard.post') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="auction_id" value="{{ $auction->id }}">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="approved" {{ $auction->state->state_name == 'approved' ? 'selected' : '' }}>approved</option>
                                            <option value="waiting" {{ $auction->state->state_name == 'waiting' ? 'selected' : '' }}>waiting</option>
                                            <option value="denied" {{ $auction->state->state_name == 'denied' ? 'selected' : '' }}>denied</option>
                                            <option value="cancelled" {{ $auction->state->state_name == 'cancelled' ? 'selected' : '' }}>cancelled</option>
                                        </select>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="report-body" id="all-auctions" style="display: none;">
                    <div class="report-topic-heading">
                        <h3 class="t-op">Auction</h3>
                        <h3 class="t-op">Bids</h3>
                        <h3 class="t-op">Creator</h3>
                        <h3 class="t-op">Status</h3>
                    </div>
                    <div class="items">
                        @foreach($auctions->sortByDesc('id') as $auction)
                            @if($auction->state_id != 4)
                                <div class="item1">
                                <h3 class="t-op-nextlvl">{{ $auction->title }}</h3>
                                    <h3 class="t-op-nextlvl">{{ $bids->where('auction_id', $auction->id)->count() }}</h3>
                                    <h3 class="t-op-nextlvl">{{ $users->firstWhere('id', $auction->owner_id)->username }}</h3> 
                                    <form class="t-op-nextlvl label-tag {{ $auction->state->state_name }}" action="{{ route('adminDashboard.post') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="auction_id" value="{{ $auction->id }}">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="approved" {{ $auction->state->state_name == 'approved' ? 'selected' : '' }}>approved</option>
                                            <option value="waiting" {{ $auction->state->state_name == 'waiting' ? 'selected' : '' }}>waiting</option>
                                            <option value="denied" {{ $auction->state->state_name == 'denied' ? 'selected' : '' }}>denied</option>
                                            <option value="cancelled" {{ $auction->state->state_name == 'cancelled' ? 'selected' : '' }}>cancelled</option>
                                        </select>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection