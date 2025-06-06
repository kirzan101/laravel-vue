<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\User;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use Illuminate\Support\Facades\DB;

class UserService
{
    use HttpErrorCodeTrait, ReturnModelCollectionTrait, ReturnModelTrait;

    public string $module = 'users';

    /**
     * Fetch a list of users with optional search functionality.
     *
     * @param array $request
     * @return array
     */
    public function indexUsers(array $request = []): array
    {
        try {
            $query = User::query();

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->get();

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $users);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single user by ID.
     *
     * @param integer $id
     * @return array
     */
    public function showUser(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $user);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Store a new user in the database.
     *
     * @param array $request
     * @return array
     */
    public function storeUser(array $request): array
    {
        try {
            DB::beginTransaction();

            // Hash password if provided
            $password = bcrypt($request['username'] ?? 'q');

            if (array_key_exists('password', $request) && !empty($request['password'])) {
                $password = bcrypt($request['password']);
            }

            $user = User::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => $password,
                'is_admin' => $request['is_admin'] ?? false,
            ]);

            DB::commit();

            return $this->returnModel(201, Helper::SUCCESS, 'User created successfully!', $user->id, $user);
        } catch (\Throwable $th) {
            DB::rollBack();

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
    public function updateUser(int $userId, array $request): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);

            $user->update([
                'username' => $request['username'] ?? $user->username,
                'email' => $request['email'] ?? $user->email,
                'password' => isset($request['password']) ? bcrypt($request['password']) : $user->password,
                'is_admin' => $request['is_admin'] ?? $user->is_admin,
            ]);

            DB::commit();

            return $this->returnModel(200, Helper::SUCCESS, 'User updated successfully!', $userId, $user);
        } catch (\Throwable $th) {
            DB::rollBack();

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
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $user->delete();

            DB::commit();

            return $this->returnModel(204, Helper::SUCCESS, 'User deleted successfully!', $userId);
        } catch (\Throwable $th) {
            DB::rollBack();

            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
