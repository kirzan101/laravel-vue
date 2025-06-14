<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\UserInterface;
use App\Models\User;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;

class UserService implements UserInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch
    ) {}

    /**
     * Store a new user in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUser(array $request): array
    {
        try {
            return DB::transaction(function () use ($request) {

                // Hash password if provided
                $password = bcrypt($request['username'] ?? 'q');

                $user = $this->base->store(User::class, [
                    'username' => $request['username'] ?? null,
                    'email' => $request['email'] ?? null,
                    'password' => $password,
                    'status' => $request['status'] ?? Helper::ACCOUNT_STATUS_ACTIVE,
                    'is_admin' => $request['is_admin'] ?? false,
                    'is_first_login' => $request['is_first_login'] ?? true,
                ]);
                return $this->returnModel(201, Helper::SUCCESS, 'User created successfully!', $user, $user->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * update an existing user in the database.
     *
     * @param integer $userId
     * @param array $request
     * @return array
     */
    public function updateUser(array $request, int $userId): array
    {
        try {
            return DB::transaction(function () use ($request, $userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                $user = $this->base->update($user, [
                    'username' => $request['username'] ?? $user->username,
                    'email' => $request['email'] ?? $user->email,
                    'password' => !empty($request['password']) ? bcrypt($request['password']) : $user->password,
                    'is_admin' => $request['is_admin'] ?? $user->is_admin,
                    'status' => $request['status'] ?? $user->status,
                    'is_first_login' => $request['is_first_login'] ?? $user->is_first_login,
                ]);

                return $this->returnModel(200, Helper::SUCCESS, 'User updated successfully!', $user, $userId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * delete a user from the database.
     *
     * @param integer $userId
     * @return array
     */
    public function deleteUser(int $userId): array
    {
        try {
            return DB::transaction(function () use ($userId) {

                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                $this->base->delete($user);

                return $this->returnModel(204, Helper::SUCCESS, 'User deleted successfully!', null, $userId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
