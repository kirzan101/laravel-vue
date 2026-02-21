<?php

namespace App\Http\Controllers;

use App\DTOs\ChangePasswordDTO;
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
        $loginResult = $this->auth->login($request->toArray());

        if ($loginResult->code == 422) {
            return back()->withErrors([
                'error' => $loginResult->message,
            ]);
        }

        return redirect()->intended();
    }

    /**
     * attempt logout
     */
    public function logout()
    {
        $logoutResult = $this->auth->logout();

        if ($logoutResult->code != 200) {
            return back()->withErrors([
                'error' => $logoutResult->message,
            ]);
        }

        return redirect()->route('login');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(ChangePasswordFormRequest $request)
    {
        // Prefer using request->filled() over exists() for clarity
        $profileId = $request->filled('profile_id')
            ? $request->input('profile_id')
            : Auth::user()->profile->id;

        // Merge the resolved profile_id back into the request so DTO sees it
        $request->merge(['profile_id' => $profileId]);

        // Build the DTO from the request
        $changePasswordDTO = ChangePasswordDTO::fromArray($request->toArray());

        // Call service
        $changePasswordResult = $this->manageAccount->changeUserProfilePassword($changePasswordDTO, $profileId);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($changePasswordResult->code, $changePasswordResult->message);

        // Handle error
        if ($changePasswordResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $changePasswordResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Success → redirect back with flash message
        return redirect()->back()->with($changePasswordResult->status, $changePasswordResult->message);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(int $userId)
    {
        $resetResult = $this->manageAccount->resetPassword($userId);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($resetResult->code, $resetResult->message);

        // Handle error
        if ($resetResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $resetResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Success → redirect back with flash message
        return redirect()->back()->with($resetResult->status, $resetResult->message);
    }

    /**
     * Set the user's active status.
     */
    public function setUserStatus(int $userId)
    {
        $statusResult = $this->manageAccount->setUserActiveStatus($userId);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($statusResult->code, $statusResult->message);

        // Handle error
        if ($statusResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $statusResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Success → redirect back with flash message
        return redirect()->back()->with($statusResult->status, $statusResult->message);
    }
}
