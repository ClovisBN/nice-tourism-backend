<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|json',
        ]);

        $document = Document::create([
            'content' => $validated['content'],
        ]);

        return response()->json($document, 201);
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return response()->json($document);
    }
}
