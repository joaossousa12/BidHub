@extends('layouts.app')

@section('content')
    <h1>Create a New Auction</h1>

    <form action="{{ route('createAuction') }}" method="POST">
        @csrf

        <label for="title">Title:</label>
        <input type="text" name="title" value="{{ old('title') }}" title="Enter a short, descriptive title for the item you're auctioning." required>

        <label for="description">Description:</label>
        <textarea name="description" title="Provide a detailed description of the item, including any important features or conditions.">{{ old('description') }}</textarea>
        
        <label for="duration">Duration:</label>
        <input type="number" name="duration" value="{{ old('duration') }}" step="0.001" title="Set the duration of the auction in days. Use decimal values for partial days." required>

        <label for="minvalue">Minimum Value:</label>
        <input type="number" name="minvalue" value="{{ old('minvalue') }}" title="Enter the starting bid amount for the item." required>

        <button type="submit">Create Auction</button>
    </form>
@endsection
