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
                    ->orderBy('id')
                    # parameter1: how many data we retrieve to you
                    # parameter2: callback function
                    ->chunk(150, function($posts){
                        // foreach($posts as $post){
                        //     dump($post->title);
                        // }
                    });
        # the callback function receives each chunk of data
        # as it argument, so every 150 post chunks will
        # be set equal to a variable named posts.
        # Using the callback function allows you to work with each
        # chunk of data separately, it can be useful for things like
        # performing calculations, filtering data, or transferring the
        # data into a different format. 

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
