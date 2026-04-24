<h1>Projects History</h1>

@foreach($projects as $project)
    <div style="background:#1e293b;padding:15px;margin:10px;border-radius:10px;">
        <h3>{{ $project->city }}</h3>

        <p>Required KW: {{ $project->required_kw }}</p>
        <p>Real KW: {{ $project->real_kw }}</p>
        <p>Panels: {{ $project->panels }}</p>
        <p>ROI: {{ $project->roi_years }} years</p>

        <a href="{{ route('projects.show', $project->id) }}">View details</a>
    </div>
@endforeach