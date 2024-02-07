@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div class="container-fluid bg-white">
<div class="bg-white mb-0 mt-4 pt-4 panel">
    <h4>
        <i class="far fa-edit"></i> Edit Profile</h4>
</div>
<hr id="hr_space" class="mt-2">
<main>
    <form class="ml-4 mr-4" method="POST" action="{{ route('users.update', ['id' => $user->id]) }}">
        {{ csrf_field() }}
        <h5 class="mb-4">Update your profile information here.</h5>
        
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ $user->username }}" title="Enter your full name." class="form-control">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="address">Address</label>
                <input id="address" type="text" name="address" value="{{ $user->address }}" title="Enter your street address including apartment number if applicable." class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="postalcode">Postal Code</label>
                <input id="postalcode" type="text" name="postalcode" value="{{ $user->postalcode }}" title="Your postal code should be in the format YYYY-YYY." class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="phonenumber">Phone Number</label>
                <input id="phonenumber" type="text" name="phonenumber" value="{{ $user->phonenumber }}" title="Enter your phone number with portuguese country code (+351), e.g., +351910910910." class="form-control">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-primary col-md-12">Update Profile</button>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('users.uploadProfilePicture', ['id' => $user->id]) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture" title="Upload a recent photograph of yourself. Preferably a square image">
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>


</main>
</div>
</div>

@endsection
