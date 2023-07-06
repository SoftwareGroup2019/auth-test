<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callbackGoogle(Request $request)
    {
        try {
            $google_user = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $google_user->getId())->first();

            if (!$user) {
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId()
                ]);

                $token = $new_user->createToken('auth-token')->plainTextToken;


                // Generate the redirect URL with the token as a query parameter
                $redirectUrl = 'https://648e0d1738df293b70152151--snazzy-mooncake-38f86f.netlify.app/login?token=' . $token;

                return redirect($redirectUrl);

                // return response()->json([
                //     "message" => "Google Authentication Done",
                //     "token" => $token
                // ], 200);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            // Generate the redirect URL with the token as a query parameter
            $redirectUrl = 'https://648e0d1738df293b70152151--snazzy-mooncake-38f86f.netlify.app/login?token=' . $token;

            return redirect($redirectUrl);

            // return response()->json([
            //     "message" => "Google Authentication Done",
            //     "token" => $token
            // ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
