<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the callback from Google
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended(route('home'));
            }
            
            // Check if user exists with same email
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Update existing user with Google ID
                $existingUser->update([
                    'google_id' => $googleUser->id,
                ]);
                
                Auth::login($existingUser);
                return redirect()->intended(route('home'));
            }
            
            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => Hash::make(uniqid()), // Random password
                'role' => 'customer',
            ]);
            
            // Create cart for new user
            Cart::create([
                'user_id' => $user->id,
            ]);
            
            Auth::login($user);
            return redirect()->intended(route('home'));
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login with Google. Please try again.');
        }
    }
}
