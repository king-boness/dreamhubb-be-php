<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            // ✅ Validácia vstupov
            $request->validate([
                'image' => 'required|file|image|max:5120',
                'post_id' => 'required|integer|exists:posts,post_id',
            ]);

            // ✅ Uloženie súboru do storage/app/public/post_images
            $path = $request->file('image')->store('post_images', 'public');

            // ✅ Uloženie záznamu do DB
            $postImageId = DB::table('post_images')->insertGetId([
                'post_id' => $request->post_id,
                'image' => $path,
                'public_id' => basename($path),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Log (len na debug)
            Log::info('🧩 Image uploaded', [
                'post_image_id' => $postImageId,
                'path' => $path,
            ]);

            // ✅ Odpoveď
            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'path' => $path,
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Image upload failed', [
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
