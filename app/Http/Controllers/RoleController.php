<?php

namespace App\Http\Controllers;

use App\DTOs\ManageRoleDTO;
use App\DTOs\RoleDTO;
use App\Helpers\Helper;
use App\Http\Requests\RoleFormRequest;
use App\Interfaces\ActivityLogInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use App\Interfaces\ManageRoleInterface;
use App\Interfaces\RoleInterface;
use App\Models\Role;
use App\Traits\ActivityLoggerTrait;
use App\Traits\ReturnMessageTrait;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RoleController extends Controller
{
    use ReturnModulePermissionTrait,
        ReturnMessageTrait,
        ActivityLoggerTrait;

    public function __construct(
        private PermissionFetchInterface $permissionFetch,
        private UserGroupFetchInterface $userGroupFetch,
        private ManageRoleInterface $manageRole,
        private ActivityLogInterface $activityLog
    ) {}

    const MODULE_NAME = 'roles';

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
        if (Gate::denies('view', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        $permissions = Cache::remember('permission_fetch_list', 60, function () {
            // Fetch the result and extract only the 'data' part
            $result = $this->permissionFetch->indexPermissions();
            return $result->data ?? []; // Only return 'data' part
        });

        $userGroups = Cache::remember('user_group_fetch_list', 60, function () {
            $result = $this->userGroupFetch->indexUserGroups();
            return $result->data ?? [];
        });

        $moduleLists = Cache::remember('module_lists', 60, function () {
            return Helper::getModuleList();
        });

        return Inertia::render('System/Roles', [
            'permissions' => $permissions,
            'user_groups' => $userGroups,
            'moduleLists' => $moduleLists,
            'can' => $this->getModulePermissions(new Role()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this role.'
            ]);
        }

        $manageRoleDTO = ManageRoleDTO::fromRequest($request);
        $result = $this->manageRole->storeRole($manageRoleDTO);

        $this->logActivity($result, $request, self::MODULE_NAME, 'store');

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this role.'
            ]);
        }

        $manageRoleDTO = ManageRoleDTO::fromRequest($request);
        $result = $this->manageRole->updateRole($manageRoleDTO, $id);

        $this->logActivity($result, $request, self::MODULE_NAME, 'update');

        return $this->returnMessage($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user has permission to delete
        if (Gate::denies('delete', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this role.'
            ]);
        }

        $result = $this->manageRole->deleteRole($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        $this->logActivity($result, $request, self::MODULE_NAME, 'delete');

        return $this->returnMessage($result);
    }
}
