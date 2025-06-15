<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\UserGroupFormRequest;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\UserGroupInterface;
use App\Services\UserGroupService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public function __construct(
        private PermissionFetchInterface $permissionFetch,
        private UserGroupInterface $userGroup
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissionsResults = $this->permissionFetch->indexPermissions($request->toArray());
        $permissions = $permissionsResults['data'] ?? [];

        return Inertia::render('System/UserGroups', [
            'permissions' => $permissions,
            'can' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserGroupFormRequest $request)
    {
        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->userGroup->storeUserGroupWithPermissions($request->toArray());

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }

        return redirect()->back()->with($status, $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserGroupFormRequest $request, int $id)
    {
        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->userGroup->updateUserGroupWithPermissions($request->toArray(), $id);

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }

        return redirect()->back()->with($status, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
