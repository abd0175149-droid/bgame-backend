<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedPlace;
use Illuminate\Http\Request;

class SavedPlaceController extends Controller
{
    /** GET /api/places — جلب أماكن اللاعب */
    public function index(Request $request)
    {
        $places = $request->user()->savedPlaces()->latest()->get();
        return response()->json($places);
    }

    /** POST /api/places — حفظ مكان جديد */
    public function store(Request $request)
    {
        $user = $request->user();

        // قاعدة: 3 أماكن كحد أقصى
        if ($user->savedPlaces()->count() >= 3) {
            return response()->json([
                'message' => 'Maximum 3 saved places allowed. Delete one first.',
            ], 422);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:50',
            'grid_data'    => 'required|array',
            'area_width'   => 'nullable|numeric|min:0',
            'area_height'  => 'nullable|numeric|min:0',
            'is_temporary' => 'boolean',
        ]);

        $place = $user->savedPlaces()->create($data);

        return response()->json($place, 201);
    }

    /** PUT /api/places/{id} — تحديث مكان */
    public function update(Request $request, SavedPlace $savedPlace)
    {
        // التحقق من الملكية
        if ($savedPlace->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name'         => 'sometimes|string|max:50',
            'grid_data'    => 'sometimes|array',
            'area_width'   => 'nullable|numeric|min:0',
            'area_height'  => 'nullable|numeric|min:0',
            'is_temporary' => 'boolean',
        ]);

        $savedPlace->update($data);

        return response()->json($savedPlace);
    }

    /** DELETE /api/places/{id} — حذف مكان */
    public function destroy(Request $request, SavedPlace $savedPlace)
    {
        if ($savedPlace->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $savedPlace->delete();

        return response()->json(['message' => 'Place deleted.']);
    }
}
