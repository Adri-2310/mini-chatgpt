<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $customInstruction = auth()->user()->customInstruction;

        \Log::info('SettingsController index', [
            'user_id' => auth()->id(),
            'has_custom_instruction' => $customInstruction ? true : false,
            'enabled_value' => $customInstruction?->enabled,
            'enabled_type' => gettype($customInstruction?->enabled),
            'raw_data' => $customInstruction ? json_encode($customInstruction->toArray()) : null,
        ]);

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

        \Log::info('SettingsController update', [
            'received_enabled' => $request->input('enabled'),
            'type_received' => gettype($request->input('enabled')),
            'filtered_enabled' => $enabled,
            'type_filtered' => gettype($enabled),
        ]);

        $result = auth()->user()->customInstruction()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'instructions' => $request->input('instructions'),
                'enabled' => $enabled ?? false,
            ]
        );

        // Recharger depuis la BD pour vérifier
        $fresh = $result->fresh();

        \Log::info('After save', [
            'saved_enabled' => $result->enabled,
            'type_saved' => gettype($result->enabled),
            'fresh_enabled' => $fresh->enabled,
            'fresh_type' => gettype($fresh->enabled),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Instructions personnalisées sauvegardées avec succès',
            'data' => [
                'enabled' => $fresh->enabled,
            ]
        ]);
    }
}
