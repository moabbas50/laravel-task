<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $UserId = Auth::user()->id;
        $posts = Post::where('user_id', '=', $UserId)->orderBy('pinned', 'desc')->get();
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'sometimes|image',
            'pinned' => 'required|boolean',
            'tags' => 'required|array|exists:tags,id',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('cover_images', 'public');
        }

        $post = Post::create($validated);
        $post->tags()->attach($request->tags);



        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);
        return response()->json($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'cover_image' => 'sometimes|image',
            'pinned' => 'sometimes|boolean',
            'tags' => 'sometimes|array|exists:tags,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('cover_images', 'public');
        }

        $post->update($validated);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }


        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
    public function trashed()
    {
        $posts =  Post::where('user_id', auth()->user->id)
            ->whereNotNull('deleted_at')
            ->get();
        return response()->json($posts);
    }
    public function restore($id)
    {

        $post = Post::where('user_id', auth()->id())
            ->where('id', $id)
            ->whereNotNull('deleted_at')
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found or not deleted'], 404);
        }


        Post::where('id', $id)
            ->update(['deleted_at' => null]);


        return response()->json(['message' => 'Post restored successfully'], 200);
    }
}
