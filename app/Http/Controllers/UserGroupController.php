<?php

namespace App\Http\Controllers;

use App\DTOs\UserGroupDTO;
use App\DTOs\UserGroupPermissionDTO;
use App\DTOs\UserGroupWithPermissionDTO;
use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use App\Http\Requests\UserGroupFormRequest;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\ManageUserGroupPermissionInterface;
use App\Interfaces\UserGroupInterface;
use App\Models\UserGroup;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    use ReturnModulePermissionTrait;

    public function __construct(
        private PermissionFetchInterface $permissionFetch,
        private ManageUserGroupPermissionInterface $manageUserGroupPermission,
        private UserGroupInterface $userGroup
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
    public function index(Request $request)
    {
        // verify the current user's group permissions
        if (Gate::denies('view', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => ErrorHelper::productionErrorMessage(403, 'You do not have permission to view this page.')
            ]);
        }

        $permissions = Cache::remember('permission_fetch_list', 60, function () {
            // Fetch the result and extract only the 'data' part
            $result = $this->permissionFetch->indexPermissions();
            return $result['data'] ?? []; // Only return 'data' part
        });

        return Inertia::render('System/UserGroups', [
            'permissions' => $permissions,
            'user_group_types' => Helper::USER_GROUP_CODE_TYPES,
            'modules' => Helper::getModuleList(),
            'can' => $this->getModulePermissions(new UserGroup())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserGroupFormRequest $request)
    {
        // Verify if the current user's group has permission to create
        if (Gate::denies('create', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create user group.'
            ]);
        }

        $userGroupDTO = UserGroupDTO::fromArray($request->all());
        $permissionIds = $request->input('permissionIds', []);
        $userGroupPermissionDTO = new UserGroupWithPermissionDTO(
            userGroup: $userGroupDTO,
            permissionIds: $permissionIds
        );

        $storeResult = $this->manageUserGroupPermission->storeUserGroupWithPermissions($userGroupPermissionDTO);

        $productionErrorMessage = ErrorHelper::productionErrorMessage($storeResult->code, $storeResult->message);
        if ($storeResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $storeResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($storeResult->status, $productionErrorMessage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserGroupFormRequest $request, int $id)
    {
        // Verify if the current user's group has permission to update
        if (Gate::denies('update', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update user group.'
            ]);
        }

        $userGroupDTO = UserGroupDTO::fromArray($request->all());
        $permissionIds = $request->input('permissionIds', []);
        $userGroupPermissionDTO = new UserGroupWithPermissionDTO(
            userGroup: $userGroupDTO,
            permissionIds: $permissionIds
        );

        $updateResult = $this->manageUserGroupPermission->updateUserGroupWithPermissions($userGroupPermissionDTO, $id);

        $productionErrorMessage = ErrorHelper::productionErrorMessage($updateResult->code, $updateResult->message);
        if ($updateResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $updateResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($updateResult->status, $productionErrorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user's group has permission to delete
        if (Gate::denies('delete', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this user group.'
            ]);
        }

        $deleteResult = $this->userGroup->deleteUserGroup($id);

        $productionErrorMessage = ErrorHelper::productionErrorMessage($deleteResult->code, $deleteResult->message);
        if ($deleteResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $deleteResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($deleteResult->status, $productionErrorMessage);
    }
}
