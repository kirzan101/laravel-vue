<?php

namespace App\Services;

use App\DTOs\UserDTO;
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
     * @param UserDTO $userDTO
     * @return array
     */
    public function storeUser(UserDTO $userDTO): array
    {
        try {
            return DB::transaction(function () use ($userDTO) {

                $userData = $userDTO->toArray(includePassword: true);
                $user = $this->base->store(User::class, $userData);

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
     * @param UserDTO $userDTO
     * @return array
     */
    public function updateUser(UserDTO $userDTO, int $userId): array
    {
        try {
            return DB::transaction(function () use ($userDTO, $userId) {
                $user = $this->fetch->showQuery(User::class, $userId)->firstOrFail();

                $userData = $userDTO->fromModel($user)->toArray();
                $user = $this->base->update($user, $userData);

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
