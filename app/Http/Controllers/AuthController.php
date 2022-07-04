<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserRecoverToken;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    /**
     * Create new user
     */
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:255',
        ];
        $this->validate($request, $rules);
        $user = new User();
        $data = $request->only(array_keys($rules));
        $data['password'] = Hash::make($data['password']);
        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * Log-in user
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|max:255',
        ];
        $this->validate($request, $rules);

        $data = $request->only(array_keys($rules));
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $hashEquals = Hash::check($data['password'], $user->password);
            if ($hashEquals) {
                $token = UserToken::updateOrCreate([
                    'user_id' => $user->id
                ],[
                    'updated_at' => Date::now(),
                    'created_at' => Date::now(),
                    'token' => Hash::make(sprintf('%s.%s.%s', $user->id, $user->password, Date::now()))
                ]);

                return $token;
            }
        }

        throw new HttpException(403, 'Wrong credentials');
    }

    /**
     * Recover request user password
     */
    public function recoverRequest(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email'
        ];
        $this->validate($request, $rules);
        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            $token = new UserRecoverToken([
                'token' => Hash::make(sprintf('%s.%s.%s', $user->id, $user->email, Date::now()))
            ]);

            $user->recoverToken()->save($token);
            return $token;
        }

        throw new HttpException(403, 'No such user');
    }


    /**
     * Recover request user password
     */
    public function recoverPassword(Request $request)
    {
        $rules = [
            'token' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ];
        $this->validate($request, $rules);
        $recoverToken = UserRecoverToken::where('token', $request->input('token'))->first();
        if ($recoverToken) {
            $user = $recoverToken->user;
            $user->password = Hash::make($request->input('password'));
            $user->save();
            //remove token to prevent change password 2nd time
            $recoverToken->delete();
            return $user;
        }

        throw new HttpException(403, 'No such token');
    }
}
