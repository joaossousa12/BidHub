@extends('layouts.app')

@section('title', 'Edit Auction')

@section('content')

<div class="container-fluid bg-white">
<div class="bg-white mb-0 mt-4 pt-4 panel">
    <h4>
        <i class="far fa-edit"></i> Edit Auction</h4>
</div>
<hr id="hr_space" class="mt-2"> 
<main>
    <form class="ml-4 mr-4" method="POST" action="{{ route('auction.edit', ['id' => $id]) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <h5 class="mb-4">Here you can submit a request to edit your ongoing auction. The request will have to be approved manually, so please be patient.
        </h5>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="description">Edit your auction's description here. Be mindful that removing information rather than adding may lead to a rejected edition request, and on the worst case scenario to your auction's cancellation.</label>
                <textarea id="description" name="description" rows="3" cols="30" class="form-control"required>{{ $desc }}</textarea>
                @if ($errors->has('description'))
                  <span class="error">
                    {{ $errors->first('description') }}
                  </span>
                @endif
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <button type="submit" class="btn btn-primary col-md-12">Submit edition request</button>
            </div>
        </div>
    </form>

</main>
</div>
</div>

@endsection