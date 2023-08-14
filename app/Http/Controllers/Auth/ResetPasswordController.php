<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ForgetPassword;
use App\Models\ResetPasswordOtp;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'email|required|exists:users,email',
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

        $user = User::where('email', $request->email)->first();

        $otpDb = ResetPasswordOtp::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        if($otpDb) {
            if($otpDb->next_send > Carbon::now()) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'message' => "next otp send at {$otpDb->next_send}",
                ], 400);
            }
        }

        $otp = rand(100000, 999999);

        DB::beginTransaction();
        
        try {
            ResetPasswordOtp::where('user_id', $user->id)->update([
                'is_valid' => false,
            ]);
        
            ResetPasswordOtp::create([
                'user_id' => $user->id,
                'token' => Hash::make($otp),
                'valid_until' => Carbon::now()->addMinutes(5),
                'next_send' => Carbon::now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new ForgetPassword($otp));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Reset password link sent to your email'
        ]);
    }

    public function changePassword(Request $request) {
        $validate = Validator::make($request->all(), [
            'email' => 'email|required|exists:users,email',
            'otp' => 'integer|required|min:6,max:8',
            'new_password' => 'required|min:8|string',
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

        $user = User::where('email', $request->email)->first();

        $otpDb = ResetPasswordOtp::where([
                ['user_id', $user->id],
                ['is_valid', true]
            ])->orderBy('created_at', 'desc')->first();

        if($otpDb->valid_until < Carbon::now()) {
            $otpDb->is_valid = false;
            $otpDb->save();

            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => "otp expired",
            ], 400);
        }

        if(!Hash::check($request->otp, $otpDb->token)) {
            $otpDb->failed_attempt += 1;
            $otpDb->save();

            if($otpDb->failed_attempt >= 3) {
                $otpDb->is_valid = false;
                $otpDb->save();
            }

            $attepLeft = 3-$otpDb->failed_attempt;

            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => "Otp is invalid, $attepLeft attempt left",
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $otpDb->is_valid = false;
        $otpDb->save();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Password changed successfully'
        ]);
    }
}
