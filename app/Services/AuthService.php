<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Traits\EnsureSuccessTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class AuthService implements AuthInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        EnsureSuccessTrait;

    /**
     * Log in a user using either username or email.
     *
     * @param array $request
     * @return array<string, mixed>
     */
    public function login(array $request): array
    {
        try {
            $isLoggedIn = false;

            if (filter_var($request['username'], FILTER_VALIDATE_EMAIL)) {
                // Attempt authentication using email
                $isLoggedIn = Auth::attempt([
                    'email' => $request['username'],
                    'password' => $request['password'],
                ]);
            } else {
                // Attempt authentication using username
                $isLoggedIn = Auth::attempt([
                    'username' => $request['username'],
                    'password' => $request['password'],
                ]);
            }

            if ($isLoggedIn) {
                if (Auth::user()->status !== Helper::ACCOUNT_STATUS_ACTIVE) {
                    Auth::logout();
                    return $this->returnModel(403, Helper::ERROR, 'Account is inactive.');
                }

                // Retrieve the user model from the database to ensure it's an Eloquent model
                $user = User::find(Auth::id());

                // Generate a new token
                $token = Str::random(60);
                $user->api_token = $token;
                $user->last_login_at = Carbon::now(); // Update last login time
                $user->save();

                Auth::getSession()->regenerate();

                return $this->returnModel(200, Helper::SUCCESS, 'Logged in successfully!', Auth::user(), Auth::id());
            }

            return $this->returnModel(422, Helper::ERROR, 'The provided credentials do not match our records.');
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return array<string, mixed>
     */
    public function logout(): array
    {
        try {
            if (Auth::check()) {

                // Invalidate the user's API token
                $user = User::find(Auth::id());
                $user->api_token = null;
                $user->last_login_at = Carbon::now(); // Update last login time
                $user->save();

                Auth::logout();

                // Use the facade for testability
                Session::invalidate();
                Session::regenerateToken();

                return $this->returnModel(200, Helper::SUCCESS, 'Logged out successfully!');
            }

            return $this->returnModel(401, Helper::ERROR, 'No authenticated user to log out.');
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * change user password
     *
     * @param string $current_password
     * @param string $new_password
     * @param integer $user_id
     * @return array
     */
    public function changePassword(string $current_password, string $new_password, int $user_id): array
    {
        try {
            return DB::transaction(function () use ($current_password, $new_password, $user_id) {
                $user = User::findOrFail($user_id);

                // check if password is valid
                $checkResult = $this->checkPasswordIsCorrect($user_id, $current_password);

                if (!$checkResult) {
                    // rollback is automatic when throwing inside transaction
                    throw new RuntimeException('Invalid current password!');
                }

                $user->update([
                    'password' => bcrypt($new_password),
                    'is_first_login' => false,
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'Successfully Changed!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return array
     */
    public function getUserByEmail(string $email): array
    {
        try {
            $user = User::where('email', $email)->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'User retrieved successfully.', $user, $user->getKey());
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Reset user password
     *
     * @param integer $user_id
     * @return array
     */
    public function resetPassword(int $user_id): array
    {
        try {
            return DB::transaction(function () use ($user_id) {
                $user = User::findOrFail($user_id);

                // set default password to username
                $new_password = bcrypt($user->username);

                $user->update([
                    'password' => $new_password,
                    'is_first_login' => true,
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'Successfully reset password!');
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Set user active status
     *
     * @param integer $user_id
     * @return array
     */
    public function setUserActiveStatus(int $user_id): array
    {
        try {
            return DB::transaction(function () use ($user_id) {
                $user = User::findOrFail($user_id);

                $currentStatus = $user->status;

                $newStatus = match ($currentStatus) {
                    Helper::ACCOUNT_STATUS_ACTIVE   => Helper::ACCOUNT_STATUS_INACTIVE,
                    Helper::ACCOUNT_STATUS_INACTIVE => Helper::ACCOUNT_STATUS_ACTIVE,
                    default => null,
                };

                if (is_null($newStatus)) {
                    throw new RuntimeException("User status is in an unexpected state: {$currentStatus}");
                }

                $user->update(['status' => $newStatus]);

                return $this->returnModel(
                    200,
                    Helper::SUCCESS,
                    "Successfully changed status to {$newStatus}!"
                );
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Get the API token of the currently authenticated user.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return Auth::user() ? Auth::user()->api_token : null;
    }

    /**
     * check if the inputed password is correct
     *
     * @param integer $user_id
     * @param string $current_password
     * @return boolean
     */
    private function checkPasswordIsCorrect(int $user_id, string $current_password): bool
    {
        $user = User::findOrFail($user_id);
        $result = Hash::check($current_password, $user->password);

        return $result;
    }
}
