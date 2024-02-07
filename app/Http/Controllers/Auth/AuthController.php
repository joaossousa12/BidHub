<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if(Auth::user()->is_admin){
                return redirect('/adminDashboard');
            }
            return redirect('/home');
        } 
        return view('auth.auth');
    }

    /**
     * Display a login form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.auth');
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            if (Auth::user()->is_banned) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been banned. Please contact support.',
                ])->onlyInput('email');
            }

            if (Auth::user()->is_admin) {
                return redirect('/adminDashboard');
            }
 
            return redirect()->route('users.showProfile', ['id' => Auth::id()]);       
         }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request){
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('auth.login')
                ->withSuccess('You have logged out successfully!');
        }

        return redirect('/home');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
            'address' => 'required|string|min:3|max:250',
            'postalcode' => 'required|string|max:8|min:8',
            'phonenumber' => 'required|string|max:13|min:13|unique:users'
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'postalcode' => $request->postalcode,
            'phonenumber' => $request->phonenumber,
            'is_admin' => false,
            'is_deleted' => false,
            'is_banned' => false
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect()->route('users.showProfile', ['id' => Auth::id()]); 
    }
}