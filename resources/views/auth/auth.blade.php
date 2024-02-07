@extends('layouts.auth')

@section('content')
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form method="POST" action="{{ route('auth.register') }}">
            {{ csrf_field() }}
            <h1>Sign up</h1>
            <input id="username" type="text" name="username" placeholder="Name" title="Enter your full name." required autofocus />
            @if ($errors->has('username'))
                <span class="error">
                    {{ $errors->first('username') }}
                </span>
            @endif

            <input id="address" type="text" name="address" placeholder="Address" title="Enter your street address including apartment number if applicable." required/>
            @if ($errors->has('address'))
                <span class="error">
                    {{ $errors->first('address') }}
                </span>
            @endif

            <input id="postalcode" type="text" name="postalcode" placeholder="Postal Code" title="Your postal code should be in the format YYYY-YYY." required/>
            @if ($errors->has('postalcode'))
                <span class="error">
                    {{ $errors->first('postalcode') }}
                </span>
            @endif

            <input id="phonenumber" type="text" name="phonenumber" placeholder="Phone Number" title="Enter your phone number with portuguese country code (+351), e.g., +351910910910." required/>
            @if ($errors->has('phonenumber'))
            <span class="error">
                {{ $errors->first('phonenumber') }}
            </span>
            @endif

            <input id="email" type="email" name="email" placeholder="E-Mail Address" title="Please enter a valid email address you have access to." required/>
            @if ($errors->has('email'))
            <span class="error">
                {{ $errors->first('email') }}
            </span>
            @endif

            <input id="password" type="password" name="password" placeholder="Password" title="Your password must have atleast 8 characters." required/>
            @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
            @endif

            <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" title="Retype the password you typed above." required/>

            <button type="submit">
                Sign Up
            </button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form method="POST" action="{{ route('auth.login') }}">
            {{ csrf_field() }}
            <h1>Sign in</h1>
            <input id="email" type="email" name="email" placeholder="Email" title="Enter the email address associated with your account." required autofocus/>
            @if ($errors->has('email'))
                <span class="error">
                {{ $errors->first('email') }}
                </span>
            @endif

            <input id="password" type="password" name="password" placeholder="Password" title="Enter your password. Remember that it's case-sensitive." required />
            @if ($errors->has('password'))
                <span class="error">
                    {{ $errors->first('password') }}
                </span>
            @endif

             <label class="rememberME"> 
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label> 
            <a class="forgotPass" href="{{ route('password.request') }}"> Forgot your password? </a> 
            <button type="submit">
                Sign In
            </button>
            @if (session('success'))
                <p class="success">
                    {{ session('success') }}
                </p>
            @endif
        </form>
    </div>
    <div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-left">
				<h1>Welcome Back!</h1>
				<p>To keep connected with us please login with your personal info!</p>
				<button class="ghost" id="signIn">Sign In</button>
			</div>
			<div class="overlay-panel overlay-right">
				<h1>Welcome to BidHub!</h1>
				<p>Enter your personal details to create a new account!</p>
				<button class="ghost" id="signUp">Sign Up</button>
			</div>
		</div>
	</div>
</div>
@endsection