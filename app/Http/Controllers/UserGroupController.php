<?php

namespace App\Http\Controllers;

use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public function __construct(private UserGroupFetchInterface $userGroupFetch) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userGroupResults = $this->userGroupFetch->indexUserGroups($request->toArray(), true);

        dd($userGroupResults);

        $userGroups = $userGroupResults['data'];

        return Inertia::render('System/UserGroups', [
            'userGroups' => $userGroups->all(),
            'per_page' => $userGroups->perPage(),
            'current_page' => $userGroups->currentPage(),
            'total' => $userGroups->total(),
            'last_page' => $userGroups->lastPage(),
            'search' => $request->search,
            'sort_by' => $request->sort_by,
            'sort_direction' => $request->sort_direction,
            'can' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
