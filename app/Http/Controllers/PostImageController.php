<?php

namespace App\Http\Controllers;

use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostImageController extends Controller
{
    public static function uploadImage($image, $post_id)
    {
        // Nahraj obrázok do storage (napr. /storage/app/public/posts)
        $path = $image->store('posts', 'public');

        // Ulož záznam do databázy
        $postImage = PostImage::create([
            'post_id' => $post_id,
            'image' => $path, // názov stĺpca v DB
            'public_id' => basename($path),
        ]);

        return $postImage;
    }

    public static function destroyPostImage($public_id)
    {
        $postImage = PostImage::where('public_id', $public_id)->first();

        if ($postImage) {
            Storage::disk('public')->delete($postImage->image);
            $postImage->delete();
        }

        return response()->json([
            'message' => 'Image deleted successfully'
        ]);
    }
}
