<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;   // ✅ Import Hash
use Illuminate\Validation\ValidationException;

class UserAuthController extends BaseController
{
    public function login(Request $request)
    {
        // validate input
        $validator = \Validator::make($request->all(), [
            'mobile'   => 'required|string',
            'type'     => 'required|in:admin,employee',
            'password' => 'required_if:type,admin|nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->failedValidation($validator);
        }

        if ($request->type === 'admin') {
            $user = User::where('mobile', $request->mobile)->first();
            
            if (! $user) {
                return $this->sendError(
                    'invalid_mobile',
                    'The mobile number is incorrect.',
                    'mobile',
                    404
                );
            }

            if (! Hash::check($request->password, $user->password)) {
                return $this->sendError(
                    'invalid_password',
                    'The password is incorrect.',
                    'password',
                    401
                );
            }

            // create token
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            return response()->json([
                'status'       => true,
                'role'         => 'admin',
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        }else{
            $employee = Employee::where('mobile', $request->mobile)->first();

            if (! $employee) {
                 return $this->sendError(
                    'invalid_mobile',
                    'The mobile number is incorrect.',
                    'mobile',
                    404
                );
            }

            return response()->json([
                'status'       => true,
                'role'         => 'employee',
                'user'         => $employee,
                'access_token' => '',
            ]);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
