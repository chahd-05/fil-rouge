<?php

namespace App\Http\Controllers;

use App\Http\Requests\EngineerCalculationRequest;
use App\Models\Project;
use App\Services\Solar\EngineerWorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EngineerController extends Controller
{
    public function __construct(
        private readonly EngineerWorkspaceService $workspace
    ) {
    }

    public function index(Request $request): View
    {
        $project = $request->filled('project')
            ? Project::findOrFail((int) $request->input('project'))
            : null;

        return view('engineer.EngineerDashboard', $this->workspace->dashboardData(
            $project,
            $request->session()->getOldInput()
        ));
    }

    public function calculate(EngineerCalculationRequest $request): RedirectResponse
    {
        $project = $this->workspace->calculateAndStore($request->validated());

        return redirect()->route('engineer.dashboard', ['project' => $project->id])
            ->with('status', 'Project calculated and saved successfully.');
    }

    public function preview(EngineerCalculationRequest $request): JsonResponse
    {
        return response()->json($this->workspace->preview($request->validated()));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('engineer.dashboard')
            ->with('status', 'Project deleted successfully.');
    }
}
