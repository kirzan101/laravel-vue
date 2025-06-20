<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserGroupResource;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function __construct(private UserGroupFetchInterface $userGroupFetch) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Set default pagination and sorting
        $request->merge([
            'per_page' => $request->get('per_page', 10),
            'sort_by' => $request->get('sort_by', 'id'),
            'sort' => $request->get('sort', 'desc'),
        ]);

        $results = $this->userGroupFetch->indexUserGroups($request->toArray(), true);

        $data = $results['data'];
        $code = $results['code'];
        $status = $results['status'];
        $message = $results['message'];
        
        return response()->json([
            'data' => UserGroupResource::collection($data->all()),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'total' => $data->total(),
            'last_page' => $data->lastPage(),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by'),
            'sort_direction' => $request->input('sort'),
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }
}
