<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointOfInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PointOfInterestController extends Controller
{
    public function index()
    {
        return PointOfInterest::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_point' => 'required|string|max:255',
            'coordinate' => 'required|json',
            'details' => 'required|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $imagePath = Storage::url($imagePath);
        } else {
            $imagePath = null;
        }

        $pointOfInterest = PointOfInterest::create([
            'name_point' => $data['name_point'],
            'coordinate' => $data['coordinate'],
            'details' => $data['details'],
            'image_path' => $imagePath,
        ]);

        return response()->json($pointOfInterest, 201);
    }

    public function show($id)
    {
        return PointOfInterest::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pointOfInterest = PointOfInterest::findOrFail($id);

        $data = $request->validate([
            'name_point' => 'sometimes|required|string|max:255',
            'coordinate' => 'sometimes|required|json',
            'details' => 'sometimes|required|json',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($pointOfInterest->image_path) {
                Storage::delete(str_replace('/storage/', 'public/', $pointOfInterest->image_path));
            }

            $imagePath = $request->file('image')->store('public/images');
            $imagePath = Storage::url($imagePath);
        } else {
            $imagePath = $pointOfInterest->image_path;
        }

        $pointOfInterest->update([
            'name_point' => $data['name_point'] ?? $pointOfInterest->name_point,
            'coordinate' => $data['coordinate'] ?? $pointOfInterest->coordinate,
            'details' => $data['details'] ?? $pointOfInterest->details,
            'image_path' => $imagePath,
        ]);

        return response()->json($pointOfInterest);
    }

    public function destroy($id)
    {
        $pointOfInterest = PointOfInterest::findOrFail($id);

        if ($pointOfInterest->image_path) {
            Storage::delete(str_replace('/storage/', 'public/', $pointOfInterest->image_path));
        }

        $pointOfInterest->delete();

        return response()->json(null, 204);
    }
}
