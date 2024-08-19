<?php

// app/Http/Controllers/ArticleController.php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all()->map(function ($article) {
            $article->image = $article->image ? asset($article->image) : null;
            return $article;
        });

        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $imagePath = Storage::url($imagePath);
        } else {
            $imagePath = null;
        }

        $article = Article::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        return response()->json($article, 201);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        $article->image = $article->image ? asset($article->image) : null;

        return response()->json($article);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::delete(str_replace('/storage/', 'public/', $article->image));
            }
            $imagePath = $request->file('image')->store('public/images');
            $imagePath = Storage::url($imagePath);
        } else {
            $imagePath = $article->image;
        }

        $article->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        return response()->json($article);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if ($article->image) {
            Storage::delete(str_replace('/storage/', 'public/', $article->image));
        }

        $article->delete();

        return response()->json(null, 204);
    }
}
