<?php
/**
 * Created by PhpStorm.
 * User: Aiden
 * Date: 2017/2/8
 * Time: 17:27
 */

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function refresh()
    {
        try {
            $oldToken = JWTAuth::getToken();
            $token = JWTAuth::refresh($oldToken);
        } catch (TokenExpiredException $e) {
            throw new ApplicationException(41001);
        } catch (JWTException $e) {
            throw new ApplicationException(40001);
        }

        return $this->responseHeader('Authorization: Bearer ' . $token)->responseData([]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('name', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                throw new ApplicationException(40002);
            }
        } catch (JWTException $e) {
            throw new ApplicationException(50001);
        }

        return $this->responseHeader('Authorization: Bearer ' . $token)->responseData([
            'user' => $request->user()
        ]);
    }

    public function signup(Request $request)
    {
        $newUser = [
            'name' => $request->get('name'),
            'nick' => $request->get('nick') ?: $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ];
        $user = User::create($newUser);
        $user->last_login_time = time();
        $user->created_at = time();
        $user->updated_at = time();
        $user->save();
        $token = JWTAuth::fromUser($user);

        return $this->responseHeader('Authorization: Bearer ' . $token)->responseData([
            'user' => $user
        ]);
    }
}
