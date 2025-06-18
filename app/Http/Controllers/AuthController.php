<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private AuthInterface $auth,
    ) {}

    /**
     * show login page
     */
    public function index()
    {
        return Inertia::render('Login');
    }

    /**
     * attempt login
     */
    public function login(Request $request)
    {
        ['code' => $code, 'message' => $message] = $this->auth->login($request->toArray());

        if ($code == 422) {
            return back()->withErrors([
                'error' => $message,
            ]);
        }

        return redirect()->intended();
    }

    /**
     * attempt logout
     */
    public function logout()
    {
        $data = $this->auth->logout();

        if ($data['code'] != 200) {
            return back()->withErrors([
                'error' => $data['message'],
            ]);
        }

        return redirect()->route('login');
    }
}
