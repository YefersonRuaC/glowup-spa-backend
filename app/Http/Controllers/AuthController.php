<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AppointmentCollection;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiTokens;

    public function register(RegisterRequest $request) 
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'admin' => false
        ]);

        // $token = $user->createToken('token')->plainTextToken;

        (new VerificationController())->sendVerificationEmail($user);

        return response([
            // 'token' => $token,
            'message' => 'User registered, check your email',
            'user' => $user
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if(!Auth::attempt($data)) {
            return response([
                'message' => 'Not valid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('cookie', $token, 60 * 24);

        return response([
            'token' => $token,
            'user'=> $user
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        $cookie = cookie('cookie', '', -1);

        return response([
            'user' => null
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    public function admin()
    {
        $user = Auth::user();

        if(!$user || !$user->admin) {
            return response([
                'error' => 'Not valid action'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response([
            'user' => $user
        ], Response::HTTP_OK);
    }

    public function userAppointments()
    {
        $user = Auth::user();
        
        if($user->admin) {
            $appointments = Appointment::with('user:id,name,email', 'services')->get();
            // dd($appointments);
        } else {
            $userId = new ObjectID($user->id);

            $appointments = Appointment::with('user:id,name,email', 'services')
                                        ->where('user_id', $userId)->get();
            // dd($appointments);
        }

        return response([
            'appointments' => new AppointmentCollection($appointments)
        ], Response::HTTP_OK);
    }
}
