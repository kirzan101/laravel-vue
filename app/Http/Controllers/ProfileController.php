<?php

namespace App\Http\Controllers;

use App\DTOs\AccountDTO;
use App\DTOs\ActivityLogDTO;
use App\DTOs\ProfileDTO;
use App\DTOs\UserDTO;
use App\Helpers\Helper;
use App\Http\Requests\ProfileFormRequest;
use App\Http\Resources\IndexResource\UserGroupIndexResource;
use App\Http\Resources\ProfileResource;
use App\Interfaces\ActivityLogInterface;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use App\Interfaces\ManageAccountInterface;
use App\Interfaces\ProfileInterface;
use App\Models\Profile;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProfileController extends Controller
{
    use ReturnModulePermissionTrait;

    public function __construct(
        private UserGroupFetchInterface $userGroupFetch,
        private ManageAccountInterface $manageAccount,
        private ActivityLogInterface $activityLog
    ) {}

    /**
     * Returns the permissions for the current user profile for the given model.
     *
     * @param Model $model The Eloquent model instance representing the target module (used to determine the table name).
     *
     * @return array An array of permission types (e.g., ['view', 'update', 'delete']) for the specified module.
     */
    protected function getModulePermissions(Model $model): array
    {
        $profileId = Auth::user()?->profile?->id;

        if (!$profileId) {
            return [];
        }

        return $this->returnPermissions($model, $profileId);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view', new Profile())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        $fetchedUserGroups = $this->userGroupFetch->indexUserGroups();

        return Inertia::render('System/Profiles', [
            'user_groups' => UserGroupIndexResource::collection($fetchedUserGroups['data'] ?? []),
            'account_types' => Helper::ACCOUNT_TYPES,
            'can' => $this->getModulePermissions(new Profile()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfileFormRequest $request)
    {
        $userDTO = UserDTO::fromArray($request->all());
        $profileDTO = ProfileDTO::fromArray($request->all());

        $accountDTO = new AccountDTO(
            user: $userDTO,
            profile: $profileDTO,
            user_group_id: $request->input('user_group_id'),
        );

        $registerResult = $this->manageAccount->register($accountDTO);

        if ($registerResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $registerResult->code,
                'message' => $registerResult->message
            ]);
        }

        // Log the activity
        $activityLogData = ActivityLogDTO::fromArray([
            'module' => 'profiles',
            'description' => $registerResult->message,
            'status' => $registerResult->status,
            'type' => 'create',
            'properties' => $request->toArray(),
        ]);
        $this->activityLog->storeActivityLog($activityLogData);

        return redirect()->back()->with($registerResult->status, $registerResult->message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileFormRequest $request, int $id)
    {
        $userDTO = UserDTO::fromArray($request->all());
        $profileDTO = ProfileDTO::fromArray($request->all());

        $accountDTO = new AccountDTO(
            user: $userDTO,
            profile: $profileDTO,
            user_group_id: $request->input('user_group_id'),
        );

        $updateResult = $this->manageAccount->updateUserProfile($accountDTO, $id);

        if ($updateResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $updateResult->code,
                'message' => $updateResult->message
            ]);
        }

        // Log the activity
        $activityLogData = ActivityLogDTO::fromArray([
            'module' => 'profiles',
            'description' => $updateResult->message,
            'status' => $updateResult->status,
            'type' => 'update',
            'properties' => $request->toArray(),
        ]);
        $this->activityLog->storeActivityLog($activityLogData);

        return redirect()->back()->with($updateResult->status, $updateResult->message);
    }
}
