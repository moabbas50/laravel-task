<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Tag::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:tags,name']);

        $tag = Tag::create(['name' => $request->name]);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Tag $tag)
    {
        $request->validate(['name' => 'required|string|unique:tags,name']);

        $tag->update(['name' => $request->name]);

        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */

     public function destroy(Tag $tag)
     {
         $tag->delete();

         return response()->json(null, 204);
     }
}
