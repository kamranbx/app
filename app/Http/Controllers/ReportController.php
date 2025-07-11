<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Report::class);
        return response()->json(Report::all(), 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $report = Report::create($validated);

        return response()->json($report, 201);
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        return response()->json($report, 200);
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
        ]);

        $report->update($validated);

        return response()->json($report, 202);
    }

    public function delete(Report $report)
    {
        $this->authorize('delete', $report);
        $report->delete();

        return response()->json(['message' => 'Report deleted'], 202);
    }

    public function special()
    {
        return response()->json(["message" => "Special privet!"]);
    }

    public function normal()
    {
        return response()->json(["message" => "Normal privet to " . auth()->user()->name . " !"]);
    }

    public function test()
    {
        return response()->json(["message" => "Privet to " . auth()->user()->name . " !"]);
    }
}
