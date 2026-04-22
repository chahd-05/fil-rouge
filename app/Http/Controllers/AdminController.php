<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'engineers' => User::where('role', 'engineer')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'projects' => Project::count(),
            ],
            'recentProjects' => Project::latest()->take(8)->get(),
        ]);
    }
}
