<?php

namespace App\Http\Controllers;

use App\DTOs\ActivityLogDTO;
use App\DTOs\ProfileDTO;
use App\Helpers\Helper;
use App\Http\Requests\ProfileFormRequest;
use App\Http\Resources\ProfileResource;
use App\Interfaces\ActivityLogInterface;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Interfaces\ProfileInterface;
use App\Models\Profile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileInterface $profile,
        private ProfileFetchInterface $profileFetch,
        private ActivityLogInterface $activityLog
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        [
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $profiles,
        ] = $this->profileFetch->indexProfiles($request->toArray(), true);

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }
        // format the profiles data
        $profiles = ProfileResource::collection($profiles);

        return Inertia::render('System/Profiles', [
            'profiles' => $profiles->all(),
            'per_page' => $profiles->perPage(),
            'current_page' => $profiles->currentPage(),
            'total' => $profiles->total(),
            'last_page' => $profiles->lastPage(),
            'search' => $request->search,
            'sort_by' => $request->sort_by,
            'sort_direction' => $request->sort_direction,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfileFormRequest $request)
    {
        $profileDTO = ProfileDTO::fromArray($request->all());

        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->profile->storeProfile($profileDTO);

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }

        // Log the activity
        $activityLogData = ActivityLogDTO::fromArray([
            'module' => 'profiles',
            'description' => $message,
            'status' => $status,
            'type' => 'delete',
            'properties' => $request->toArray(),
        ]);
        $this->activityLog->storeActivityLog($activityLogData);

        return redirect()->back()->with($status, $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileFormRequest $request, int $id)
    {
        $profileDTO = ProfileDTO::fromArray($request->all());

        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->profile->updateProfile($profileDTO, $id);

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }

        // Log the activity
        $activityLogData = ActivityLogDTO::fromArray([
            'module' => 'profiles',
            'description' => $message,
            'status' => $status,
            'type' => 'delete',
            'properties' => $request->toArray(),
        ]);
        $this->activityLog->storeActivityLog($activityLogData);

        return redirect()->back()->with($status, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->profile->deleteProfile($id);

        if ($status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $message
            ]);
        }

        // Log the activity
        $activityLogData = ActivityLogDTO::fromArray([
            'module' => 'profiles',
            'description' => $message,
            'status' => $status,
            'type' => 'delete',
            'properties' => [
                'profile_id' => $id,
            ],
        ]);
        $this->activityLog->storeActivityLog($activityLogData);

        return redirect()->back()->with($status, $message);
    }
}
