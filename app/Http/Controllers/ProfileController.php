<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
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
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [
            'code' => $code,
            'status' => $status,
            'message' => $message
        ] = $this->profile->storeProfile($request->all());

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
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
