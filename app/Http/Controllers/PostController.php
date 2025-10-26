<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    // 🟢 CREATE POST (s obrázkami)
    public function createPost(Request $request)
    {
        try {
            $user = auth()->user();

            // ✅ Validácia vstupov
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'date_deadline' => 'nullable|date',
                'category_id' => 'required|integer|exists:categories,category_id',
                'images.*' => 'nullable|file|image|max:5120', // max 5 MB
            ]);

            // ✅ Vloženie postu
            DB::table('posts')->insert([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'user_id' => $user->id,
                'date_created' => now()->toDateString(),
                'date_deadline' => $validated['date_deadline'] ?? null,
                'category_id' => $validated['category_id'],
                'tokens' => 0,
                'views' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Získanie ID posledného vloženého postu
            $postId = DB::getPdo()->lastInsertId();

            $uploadedImages = [];

            // ✅ Ak request obsahuje obrázky
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('post_images', 'public');
                    $publicId = basename($path);

                    DB::table('post_images')->insert([
                        'post_id' => $postId,
                        'image' => $path,
                        'public_id' => $publicId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $uploadedImages[] = $path;
                }
            }

            // ✅ Úspešná odpoveď
            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully',
                'post_id' => $postId,
                'uploaded_images' => $uploadedImages,
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Post creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // 🟦 GET ALL POSTS (feed)
    public function getAllPosts()
    {
        try {
            $posts = DB::table('posts')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->join('categories', 'posts.category_id', '=', 'categories.category_id')
                ->leftJoin('post_images', 'posts.post_id', '=', 'post_images.post_id')
                ->select(
                    'posts.post_id',
                    'posts.title',
                    'posts.description',
                    'posts.date_created',
                    'posts.date_deadline',
                    'posts.tokens',
                    'posts.views',
                    'categories.name as category_name',
                    'users.username as author_name',
                    DB::raw('array_agg(post_images.image) as images')
                )
                ->groupBy(
                    'posts.post_id',
                    'posts.title',
                    'posts.description',
                    'posts.date_created',
                    'posts.date_deadline',
                    'posts.tokens',
                    'posts.views',
                    'categories.name',
                    'users.username'
                )
                ->orderBy('posts.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'count' => count($posts),
                'posts' => $posts,
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Fetch posts failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // 🟨 GET SINGLE POST (detail)
    public function getPost($id)
    {
        try {
            // 🔍 Získaj post podľa ID
            $post = DB::table('posts')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->join('categories', 'posts.category_id', '=', 'categories.category_id')
                ->leftJoin('post_images', 'posts.post_id', '=', 'post_images.post_id')
                ->select(
                    'posts.post_id',
                    'posts.title',
                    'posts.description',
                    'posts.date_created',
                    'posts.date_deadline',
                    'posts.tokens',
                    'posts.views',
                    'categories.name as category_name',
                    'users.username as author_name',
                    DB::raw('array_agg(post_images.image) as images')
                )
                ->where('posts.post_id', '=', $id)
                ->groupBy(
                    'posts.post_id',
                    'posts.title',
                    'posts.description',
                    'posts.date_created',
                    'posts.date_deadline',
                    'posts.tokens',
                    'posts.views',
                    'categories.name',
                    'users.username'
                )
                ->first();

            // ❌ Ak post neexistuje
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found',
                ], 404);
            }

            // ✅ Úspešná odpoveď
            return response()->json([
                'status' => 'success',
                'post' => $post,
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Fetch single post failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // 🟧 UPDATE POST (editácia)
    public function updatePost(Request $request, $id)
    {
        try {
            $user = auth()->user();

            // 🧩 Skontroluj, či post patrí tomuto userovi
            $post = DB::table('posts')->where('post_id', $id)->first();
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found.',
                ], 404);
            }
            if ($post->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized: You can only edit your own posts.',
                ], 403);
            }

            // ✅ Validácia vstupov
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'date_deadline' => 'nullable|date',
                'category_id' => 'nullable|integer|exists:categories,category_id',
                'images.*' => 'nullable|file|image|max:5120',
            ]);

            // ✅ Aktualizácia postu
            DB::table('posts')
                ->where('post_id', $id)
                ->update([
                    'title' => $validated['title'] ?? $post->title,
                    'description' => $validated['description'] ?? $post->description,
                    'date_deadline' => $validated['date_deadline'] ?? $post->date_deadline,
                    'category_id' => $validated['category_id'] ?? $post->category_id,
                    'updated_at' => now(),
                ]);

            // ✅ Ak sú nové obrázky → zmaž staré a pridaj nové
            if ($request->hasFile('images')) {
                DB::table('post_images')->where('post_id', $id)->delete();

                foreach ($request->file('images') as $image) {
                    $path = $image->store('post_images', 'public');
                    $publicId = basename($path);

                    DB::table('post_images')->insert([
                        'post_id' => $id,
                        'image' => $path,
                        'public_id' => $publicId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Post update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
