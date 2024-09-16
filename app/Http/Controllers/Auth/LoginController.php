<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Users\Requests\UpdateUserRequest;
use App\Domain\Users\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|exists:users,login',
            'password' => 'required',
        ]);

        if (Auth::attempt(['login' => $request->login, 'password' => $request->password])) {
            $user = User::where('login', $request->login)->first();

            $token = $user->createToken('token-name', [$user->login])->plainTextToken;
            return $this->successResponse([
                'token' => $token,
            ], new UserResource($user));
        }
        return $this->errorResponse('Login or password error', 404);
    }

    public function updateLoginPassword(UpdateUserRequest $request, User $user)
    {
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect');
        }

        // Update password
        $user->login = $request->login;
        $user->password = Hash::make($request->new_password);
        $user->update();

        return $this->successResponse('Password updated successfully', new UserResource($user));
    }
}
