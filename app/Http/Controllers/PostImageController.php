<?php

namespace App\Http\Controllers;

use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostImageController extends Controller
{
    public static function uploadImage($image, $post_id)
    {
        try {
            // 1️⃣ Nahraj obrázok do storage (napr. /storage/app/public/posts)
            $path = $image->store('posts', 'public');

            // 2️⃣ Ulož záznam do databázy
            $postImage = PostImage::create([
                'post_id' => $post_id,
                'image' => $path,
                'public_id' => basename($path),
            ]);

            // 3️⃣ Zaloguj výsledok (pomôže pri debugovaní)
            Log::info('✅ Image uploaded successfully', [
                'post_id' => $post_id,
                'path' => $path,
                'public_id' => basename($path),
            ]);

            // 4️⃣ Vráť odpoveď pre FE/Postman
            return response()->json([
                'message' => 'Image uploaded successfully',
                'post_id' => $post_id,
                'image' => $path,
                'public_id' => basename($path),
            ], 200);

        } catch (\Exception $e) {
            // 5️⃣ Ak niečo zlyhá, zapíš chybu do logu
            Log::error('❌ Image upload failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Image upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function destroyPostImage($public_id)
    {
        $postImage = PostImage::where('public_id', $public_id)->first();

        if ($postImage) {
            Storage::disk('public')->delete($postImage->image);
            $postImage->delete();

            return response()->json([
                'message' => 'Image deleted successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Image not found'
        ], 404);
    }
}
