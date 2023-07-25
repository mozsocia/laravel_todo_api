<?php

namespace App\Http\Controllers;

use App\Helpers\JWTToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Create a new user
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->otp = $request->otp;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successfull',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Find the user
            $user = User::where('email', $request->email)->first();

            // Check if user exists and verify password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Generate and return JWT token
            $token = JWTToken::createToken(['user_id' => $user->id, 'user_email' => $user->email]);
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Succesfull',
                'token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized',
                'error' => 'Login failed',
            ], 500);
        }
    }
}
