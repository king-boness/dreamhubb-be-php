<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getPosts()
    {
        $posts = Post::with('postImages', 'category', 'user.userBadge')->get();
        return response()->json([
            'posts' => $posts
        ], 200);
    }

    public function getPost($id)
    {
        $post = Post::find($id);
        return response()->json([
            'post' => $post
        ], 200);
    }

    public function getMyPosts()
    {
        $user = Auth::user();
        $posts = Post::where('user_id', $user->id)->get();
        return response()->json([
            'posts' => $posts
        ], 200);
    }

    public function createPost(Request $request)
    {
        $user = auth()->user();
        // $validated = $request->validate([
        //     'title' => 'required|string',
        //     'description' => 'required|string',
        //     'date_created' => 'required|string',
        //     'date_deadline' => 'required|string',
        //     'category_id' => 'required|integer',
        //     'tokens' => 'required|integer',
        //     'views' => 'required|integer'
        // ]);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
            'date_created' => date('Y-m-d'),
            'date_deadline' => $request->date_deadline,
            'category_id' => $request->category_id,
        ]);

        if ($request->has('images')) {
            foreach ($request->images as $image) {
                PostImageController::uploadImage($image, $post->post_id);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'post_id' => $post->post_id
        ], 200);
    }

    public function updatePost(Request $request, $id)
    {
        $post = Post::find($id);
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'date_created' => $request->date_created,
            'date_deadline' => $request->date_deadline,
            'category_id' => $request->category_id,
            'tokens' => $request->tokens,
            'views' => $request->views
        ]);
        return response()->json([
            'post' => $post
        ], 200);
    }

    public function deletePost($id)
    {
        $post = Post::find($id);
        $postImages = PostImage::where('post_id', $id)->get();
        foreach ($postImages as $postImage) {
            PostImageController::destroyPostImage($postImage->public_id);
            $postImage->delete();
        }
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
