<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectIndexRequest;
use App\Models\Project;
use App\Services\Solar\ProjectCatalogService;
use App\Services\Solar\SolarService;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectCatalogService $catalog,
        private readonly SolarService $solarService
    ) {
    }

    public function index(ProjectIndexRequest $request): View
    {
        $filters = $request->validated();

        return view('projects.history', [
            'projects' => $this->catalog->paginate($filters),
            'filters' => $filters,
            'cities' => $this->catalog->availableCities(),
        ]);
    }

    public function show(Project $project): View
    {
        return view('projects.show', [
            'project' => $project,
            'dashboardPayload' => $this->solarService->projectToDashboardPayload($project),
        ]);
    }
}
