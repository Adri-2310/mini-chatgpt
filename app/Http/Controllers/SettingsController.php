<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $customInstruction = auth()->user()->customInstruction;

        return Inertia::render('Settings', [
            'customInstruction' => $customInstruction,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'instructions' => 'nullable|string|max:2000',
            'enabled' => 'required|boolean',
        ]);

        $enabled = filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $result = auth()->user()->customInstruction()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'instructions' => $request->input('instructions'),
                'enabled' => $enabled ?? false,
            ]
        );

        $fresh = $result->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Instructions personnalisées sauvegardées avec succès',
            'data' => [
                'enabled' => $fresh->enabled,
            ]
        ]);
    }
}
