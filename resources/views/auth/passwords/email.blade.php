@extends('layouts.auth')

@section('content')

    <div class="email-container">
        <h2>Reset Password</h2>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">E-Mail Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" title="Type the email you want us to send the password recovery link to." name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Send Password Reset Link
            </button>
        </form>

        @if(session('status'))
            <div class="alert alert-success-reset" role="alert">
                <p>{{ session('status') }} Go check your email to continue with password reset process.</p>
            </div>
        @endif  
    </div>

@endsection
