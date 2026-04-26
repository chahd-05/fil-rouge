<?php

namespace App\Services\Solar;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProjectCatalogService
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 10);

        return Project::query()
            ->when($filters['city'] ?? null, function (Builder $query, string $city): void {
                $query->where('city', $city);
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where('city', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function availableCities(): Collection
    {
        return Project::query()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }
}
