<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     * Includes state parameter for CSRF protection
     */
    public function redirectToGoogle(Request $request)
    {
        // Store return URL for post-login redirect
        $returnUrl = $request->input('return_url', null);
        if ($returnUrl) {
            session(['google_return_url' => $returnUrl]);
        }
        
        // Generate state parameter for CSRF protection
        $state = Str::random(40);
        session(['oauth_state' => $state]);
        
        return Socialite::driver('google')
            ->stateless()
            ->with([
                'prompt' => 'select_account',
                'access_type' => 'online',
                'redirect_uri' => config('services.google.redirect'),
            ])
            ->redirect();
    }

    /**
     * Handle the callback from Google
     * Validates state parameter and processes user
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Validate state parameter for CSRF protection
            // Note: Stateless mode doesn't use traditional session state
            // The 'stateless()' method handles this automatically
            
            // Check for errors from Google
            if ($request->has('error')) {
                $error = $request->get('error');
                Log::warning('Google OAuth error: ' . $error);
                
                if ($error === 'access_denied') {
                    return redirect()->route('login')->with('error', 'Google sign-in was cancelled.');
                }
                
                return redirect()->route('login')->with('error', 'Failed to authenticate with Google.');
            }
            
            // Get the authorization code
            $code = $request->get('code');
            if (!$code) {
                return redirect()->route('login')->with('error', 'No authorization code received from Google.');
            }
            
            // Get user info from Google using stateless authentication
            $googleUser = Socialite::driver('google')->stateless()
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();
            
            // Validate required data from Google
            if (!$googleUser->getEmail()) {
                Log::error('Google OAuth: No email received');
                return redirect()->route('login')->with('error', 'Failed to get email from Google.');
            }
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // CASE 1: Existing user with email/password - link Google account
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                    Log::info('Linked Google account to existing user: ' . $user->email);
                } else {
                    // CASE 2: Existing Google user - update avatar if changed
                    if ($user->avatar !== $googleUser->getAvatar()) {
                        $user->update(['avatar' => $googleUser->getAvatar()]);
                    }
                }
                
                // Log in the existing user
                $this->loginUser($user);
                
            } else {
                // CASE 3: New user - create account
                $user = User::create([
                    'name' => $googleUser->getName() ?? explode('@', $googleUser->getEmail())[0],
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null, // No password for Google users
                    'role' => 'customer',
                    'email_verified_at' => now(), // Google users are pre-verified
                ]);
                
                // Create cart for new user
                Cart::create([
                    'user_id' => $user->id,
                ]);
                
                Log::info('Created new Google user: ' . $user->email);
                
                // Log in the new user
                $this->loginUser($user);
            }
            
            // Clear OAuth state from session
            session()->forget('oauth_state');
            
            // Redirect based on role or stored return URL
            return $this->redirectBasedOnRole($user);
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('Google OAuth Invalid State: ' . $e->getMessage());
            // Clear invalid state and redirect to login
            session()->forget('oauth_state');
            return redirect()->route('login')->with('error', 'Authentication session expired. Please try again.');
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Google OAuth Client Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Failed to communicate with Google. Please try again.');
            
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Failed to login with Google. Please try again.');
        }
    }
    
    /**
     * Log in user with remember me functionality
     */
    protected function loginUser(User $user, $remember = false)
    {
        // Clear any existing sessions for this user (security)
        // Auth::logoutOtherDevices($user->password);
        
        // Login the user
        Auth::login($user, $remember);
        
        // Regenerate session to prevent session fixation
        $request = request();
        $request->session()->regenerate();
    }
    
    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(User $user)
    {
        // Check for stored return URL
        $returnUrl = session('google_return_url');
        if ($returnUrl) {
            session()->forget('google_return_url');
            return redirect()->to($returnUrl);
        }
        
        // Role-based redirection
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // For customers, redirect to products page or home
        return redirect()->intended(route('products.index'));
    }
    
    /**
     * Get user's Google refresh token from database
     * Note: You may need to store the refresh token if needed
     */
    public function getGoogleRefreshToken(User $user)
    {
        // If you store refresh tokens, retrieve them here
        // Socialite doesn't store them by default
        return null;
    }

    public function googleAuthentication() {
        $googleUser = Socialite::driver('google')->user();
        dd($googleUser);
    }
}
