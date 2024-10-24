<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = DB::table('posts')
            ->select('user_id', DB::raw('AVG(min_to_read) AS avg_mintoread'))
            ->groupByRaw('user_id')
            ->get();

        // $posts = DB::table('posts')
        //     ->select('user_id', DB::raw( 'SUM(min_to_read) AS total_time' ) )
        //     ->groupBy('user_id')
        //     ->havingRaw('total_time > 5 ')
        //     ->get();
        // $posts = DB::table('posts')
        //     ->whereRaw('created_at > now() - INTERVAL 1 DAY')
        //     ->get();
        // $posts = DB::table('posts')
        //     ->selectRaw('count(*) as post_count')
        //     ->first();

        
        # returns true
        dd($posts);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
