<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class statsController extends Controller
{
    public function stats()
    {
        $stats = cache()->rememberForever('stats', function () {
            return [
                'total_users' => \App\Models\User::count(),
                'total_posts' => \App\Models\Post::count(),
                'users_with_no_posts' => \App\Models\User::doesntHave('posts')->count(),
            ];
        });

        return response()->json($stats);
    }
}
