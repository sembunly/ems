# Google Auth Implementation Plan

## Overview
Align the existing Google OAuth implementation with the documented flow from `docs/Google_Auth.doc`.

## Current Status
- ✅ Laravel Socialite installed
- ✅ Google credentials configured in `.env`
- ✅ `google_id` column added to users table
- ✅ Basic controller structure exists
- ⚠️ Routes don't match documented paths
- ⚠️ Controller logic differs from documented flow
- ⚠️ Password is not nullable for Google users

## Required Changes

### 1. Update Routes (`routes/auth.php`)

**Current:**
```php
Route::get('google/redirect', [SocialAuthController::class, 'redirectToGoogle'])
    ->name('google.redirect');
Route::get('google/callback', [SocialAuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');
```

**Change to:**
```php
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])
    ->name('google.redirect');
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');
```

### 2. Update `.env` Redirect URI

**Current:**
```
GOOGLE_REDIRECT_URI=http://localhost:8000/google/callback
```

**Change to:**
```
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 3. Create Migration for Nullable Password

Create new migration to make password nullable:
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('password')->nullable()->change();
});
```

### 4. Update SocialAuthController

**Current Logic:**
1. Look up user by `google_id`
2. If not found, look up by email
3. Create user with random password
4. Create cart for new user

**Documented Logic:**
1. Look up user by email
2. If not found, create user with NULL password
3. Update google_id if user exists (optional)
4. Login user
5. Redirect to home/dashboard

**Update `handleGoogleCallback()` to:**
```php
public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        // Look up user by email (as per documentation)
        $user = User::where('email', $googleUser->getEmail())->first();
        
        if (!$user) {
            // Create new user with NULL password (as per documentation)
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => null,
                'role' => 'customer',
            ]);
            
            // Optional: Create cart for new user (keep existing feature)
            Cart::create(['user_id' => $user->id]);
            
            // Optional: Send welcome email (as per documentation step 11)
            // Mail::to($user->email)->send(new WelcomeEmail($user));
        } else {
            // Update google_id if not set (as per documentation step 8)
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }
        
        Auth::login($user);
        return redirect()->intended(route('home'));
        
    } catch (\Exception $e) {
        return redirect()->route('login')
            ->with('error', 'Failed to login with Google. Please try again.');
    }
}
```

### 5. Update Login View

**Current button:**
```blade
<a href="{{ route('google.redirect') }}" ...>
```

This should continue to work as the route name remains `google.redirect`.

But we should verify the URL matches the documentation:
```blade
<a href="/auth/google">
  Login with Google
</a>
```

### 6. Update Postman Collection

Update the Google OAuth endpoints in the Postman collection:
- Change `/google/redirect` to `/auth/google`
- Change `/google/callback` to `/auth/google/callback`

## Flow Diagram

```mermaid
flowchart TD
    A[User clicks Login with Google] --> B[Redirect to /auth/google]
    B --> C[Socialite redirects to Google]
    C --> D[User authenticates with Google]
    D --> E[Google redirects to /auth/google/callback]
    E --> F[Get user data from Google]
    F --> G{User exists by email?}
    G -->|No| H[Create new user with NULL password]
    H --> I[Create cart for user]
    G -->|Yes| J{google_id set?}
    J -->|No| K[Update google_id]
    J -->|Yes| L[Skip update]
    K --> M[Auth::login\(\)]
    L --> M
    I --> M
    M --> N[Redirect to home]
```

## Testing Steps

1. Clear config cache: `php artisan config:clear`
2. Run migration: `php artisan migrate`
3. Test Google login flow
4. Verify new Google user has NULL password
5. Verify existing user gets google_id updated
6. Check redirect goes to home page

## Files to Modify

1. `routes/auth.php` - Update route paths
2. `.env` - Update GOOGLE_REDIRECT_URI
3. Create migration for nullable password
4. `app/Http/Controllers/Auth/SocialAuthController.php` - Update logic
5. `laptop_store_postman_collection.json` - Update endpoint paths

## Notes

- The existing cart creation for new users is kept as it's a nice feature
- Welcome email is mentioned in documentation but can be added later
- The route names (`google.redirect`, `google.callback`) remain unchanged for backward compatibility
