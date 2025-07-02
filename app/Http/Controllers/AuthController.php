<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use App\Http\Requests\System\ChangePasswordFormRequest;
use App\Interfaces\AuthInterface;
use App\Interfaces\ManageAccountInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private AuthInterface $auth,
        private ManageAccountInterface $manageAccount
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

    /**
     * change user password
     */
    public function changePassword(ChangePasswordFormRequest $request)
    {
        $profileId = Auth::user()->profile->id;

        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->manageAccount->changeUserProfilePassword($request->toArray(), $profileId);

        $productionErrorMessage = ErrorHelper::productionErrorMessage($code, $message);
        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($status, $message);
    }
}
