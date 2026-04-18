<?php

namespace App\Http\Controllers;

use App\DTOs\ModuleDTO;
use App\Helpers\Helper;
use App\Http\Requests\ModuleFormRequest;
use App\Interfaces\ActivityLogInterface;
use App\Interfaces\ModuleInterface;
use App\Models\Module;
use App\Traits\ActivityLoggerTrait;
use App\Traits\ReturnMessageTrait;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ModuleController extends Controller
{
    use ReturnModulePermissionTrait,
        ReturnMessageTrait,
        ActivityLoggerTrait;

    public function __construct(
        private ModuleInterface $module,
        private ActivityLogInterface $activityLog
    ) {}

    const MODULE_NAME = 'modules';

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
        if (Gate::denies('view', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        return Inertia::render('Modules', [
            'can' => $this->getModulePermissions(new Module()),
            'categories' => Helper::MODULE_CATEGORIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModuleFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this module.'
            ]);
        }

        $moduleDTO = ModuleDTO::fromRequest($request);
        $result = $this->module->storeModule($moduleDTO);

        $this->logActivity($result, $request, self::MODULE_NAME, 'store');

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModuleFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this module.'
            ]);
        }

        $moduleDTO = ModuleDTO::fromRequest($request);
        $result = $this->module->updateModule($moduleDTO, $id);

        $this->logActivity($result, $request, self::MODULE_NAME, 'update');

        return $this->returnMessage($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user has permission to delete
        if (Gate::denies('delete', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this module.'
            ]);
        }

        $result = $this->module->deleteModule($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        $this->logActivity($result, $request, self::MODULE_NAME, 'delete');

        return $this->returnMessage($result);
    }
}
