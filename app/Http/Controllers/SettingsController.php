<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings', [
            'customInstruction' => auth()->user()->customInstruction,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'instructions' => 'required|string|max:2000',
            'enabled' => 'required|boolean',
        ]);

        auth()->user()->customInstruction()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'instructions' => $request->input('instructions'),
                'enabled' => $request->input('enabled'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Instructions personnalisées sauvegardées avec succès',
        ]);
    }
}
