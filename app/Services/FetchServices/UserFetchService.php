<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\UserFetchInterface;
use App\Models\User;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;

class UserFetchService extends BaseFetchService implements UserFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of users with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @return array
     */
    public function indexUsers(array $request = [], bool $isPaginated = false): array
    {
        try {
            $query = $this->indexQuery(User::class);

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new User())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort
                ] = $this->paginateFilter($request, $allowedFields);

                $users = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $users = $query->get();
            }

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
            $query = $this->showQuery(User::class, $userId);

            $user = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $user, $userId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
