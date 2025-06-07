<?php

namespace App\Services\FetchServices;

use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Models\Profile;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;

class ProfileFetchService extends BaseFetchService implements ProfileFetchInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of profiles with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @return array
     */
    public function indexProfiles(array $request = [], bool $isPaginated = false): array
    {
        try {
            $query = $this->indexQuery(Profile::class);

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('nickname', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Profile())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort
                ] = $this->paginateFilter($request, $allowedFields);

                $profiles = $query->orderBy($sort_by, $sort)->paginate($per_page);
            } else {

                $profiles = $query->get();
            }

            return $this->returnModelCollection(200, Helper::SUCCESS, 'Successfully fetched!', $profiles);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModelCollection($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single Profile by ID.
     *
     * @param integer $id
     * @return array
     */
    public function showProfile(int $profileId): array
    {
        try {
            $query = $this->showQuery(Profile::class, $profileId);

            $profile = $query->firstOrFail();

            return $this->returnModel(200, Helper::SUCCESS, 'Successfully fetched!', $profile, $profileId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return $this->returnModel($code, Helper::ERROR, $th->getMessage());
        }
    }
}
