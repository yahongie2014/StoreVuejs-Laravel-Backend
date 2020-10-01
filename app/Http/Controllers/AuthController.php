<?php

namespace App\Http\Controllers;

use Alkoumi\LaravelHijriDate\Hijri;
use App\Http\Requests\UserValdation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->middleware('auth:api')->only('logout');

    }

    public function register(UserValdation $request)
    {

        $user = new $this->user($request->all());
        $user->password = Hash::make($request->password);
        $user->name_ar = $request->name_ar;
        $user->email = $request->phone . '@gmail.com';
        $user->date = $request->date;
        if ($user->save()) {
            DB::commit();
        }
        return response()->json([
            'registered' => true,
        ]);

    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|min:11|max:13',
            'password' => 'required|between:6,25'
        ]);

        $user = User::where('phone', $request->phone)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // generate new api token
            $user->api_token = str_random(60);
            $user->save();

            return response()
                ->json([
                    'authenticated' => true,
                    'api_token' => $user->api_token,
                    'user_id' => $user->id
                ]);
        }

        return response()
            ->json([
                'phone' => ['Provided Phone Or password does not match!']
            ], 422);

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()
            ->json([
                'done' => true
            ]);
    }

    public function SocialSignup($provider)
    {
        $user_social = Socialite::driver($provider)->stateless()->user();
        $exist = $this->user->where('email', $user_social->email)->first();
        if ($exist) {

            if ($exist && Hash::check($user_social->id, $exist->password)) {
                $exist->api_token = str_random(60);
                $exist->save();
            }

            return response()
                ->json([
                    'authenticated' => true,
                    'api_token' => $exist->api_token,
                    'user_id' => $exist->id
                ]);

        } else {
            $date = Carbon::now()->addMonth();
            $user = new $this->user();
            $user->password = Hash::make($user_social->id);
            $user->email = $user_social->email;
            $user->name_ar = $user_social->name;
            $user->name = $user_social->name;
            $user->phone = '01' . rand(100000, 99999);
            $user->date = Hijri::DateShortFormat('ar', $date);
            if ($user->save()) {
                DB::commit();
                if ($user && Hash::check($user_social->id, Hash::make($user_social->id))) {
                    $user->api_token = str_random(60);
                    $user->save();
                }
            }
            return response()
                ->json([
                    'authenticated' => true,
                    'api_token' => $user->api_token,
                    'user_id' => $user->id
                ]);
        }
    }

    public
    function callback()
    {
        return view('welcome');
    }
}
