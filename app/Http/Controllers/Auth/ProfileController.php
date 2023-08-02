<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'User profile successfully',
                'data' => $user
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'old_password' => 'required|min:8|string',
            'new_password' => 'required|min:8|string'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $errorMessage = [];

            foreach ($errors->all() as $message) {
                $errorMessage[] = $message;
            }

            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Unprocessable Entity',
                'errors' => $errorMessage
            ], 422);
        }

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'Bad Request',
                'errors' => ['credential not match to our record']
            ], 400);
        }

        $user = User::find($user->id);

        $user = $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Password changed successfully',
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:3|max:55',
            'email' => 'email|required|unique:users,email,'.$user->id,
            'phone' => 'required|min:10|max:12'
        ]);

        if ($validate->fails()) {
            $errors = $validate->errors();
            $errorMessage = [];

            foreach ($errors->all() as $message) {
                $errorMessage[] = $message;
            }

            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Unprocessable Entity',
                'errors' => $errorMessage
            ], 422);
        }

        $user = User::find($user->id);

        $user = $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
}
