<?php

namespace App\Http\Controllers;

use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\PermissionInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public function __construct(private PermissionFetchInterface $permissionFetch) {}

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
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
