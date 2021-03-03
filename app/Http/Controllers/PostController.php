<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\Category;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post = Post::all();

        return response()->json(["data"=>$post]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'body'=>'required',
            'category_id'=>'required',
        ]);

        $inputs = $request->all();

        $inputs['slug'] = \Str::slug($request->title);
        $count = Post::where('slug', $inputs['slug'])->count();
        if ($count > 0) {
            $inputs['slug'] .= '-' . date('YmdHis');
        }

        if(!Category::find($inputs['category_id'])){
            return response()->json(["status"=>"failed", "message"=>"Category not found"]);
        }

        $inputs['user_id'] = $user->id;
    
        $created = Post::create($inputs);

        if($created){
            return response()->json(['status'=>'success', 'message'=>'Create post successfuly']);
        }
        else{
            return response()->json(['status'=>'failed', 'message'=>'something wrong, create post fail']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json(["data"=>$post]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return response()->json(["data"=>$post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();

        $this->authorize('post_owner', $post);

        $validator = Validator::make($request->all(), [
            'title'=>'required',
            'body'=>'required',
            'category_id'=>'required',
        ]);

        $inputs = $request->all();

        $inputs['user_id'] = $user->id;

        if(!Category::find($inputs['category_id'])){
            return response()->json(["status"=>"failed", "message"=>"Category not found"]);
        }

        $inputs['slug'] = \Str::slug($request->title);
        $count = Post::where('slug', $inputs['slug'])->count();
        if ($count > 0) {
            $inputs['slug'] .= '-' . date('YmdHis');
        }

        $post->update($inputs);

        return response()->json(["status"=>"success","message"=>"Update successfully", "data"=>$post]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('post_owner', $post);

        $post->delete();

        return response()->json(["status"=>"success", "message"=>"Delete successfully"]);
    }
}
