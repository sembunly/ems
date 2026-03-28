<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class OAuthLogoutController extends Controller
{
    /**
     * Handle logout for both regular and Google users
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        // Attempt to revoke Google token if user has one
        if ($user && $user->google_id) {
            try {
                // Note: Revoking Google token requires the refresh token
                // which Socialite doesn't store by default
                // If you stored it, you can revoke it here
                // Socialite::driver('google')->revoke();
                
                Log::info('User logged out (Google user): ' . $user->email);
            } catch (\Exception $e) {
                Log::warning('Could not revoke Google token: ' . $e->getMessage());
            }
        } else {
            Log::info('User logged out: ' . ($user ? $user->email : 'Unknown'));
        }
        
        // Logout from application
        Auth::guard('web')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
