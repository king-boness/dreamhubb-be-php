<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\UploadController;

class PostImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'post_id' => 'required|integer|exists:posts,post_id',
            ]);

            // 🔹 Zavoláme centrálny UploadController
            $uploadController = new UploadController();
            $uploadResponse = $uploadController->upload($request);
            $uploadData = $uploadResponse->getData();

            if ($uploadData->status !== 'success') {
                return $uploadResponse;
            }

            // 🔹 Uloženie záznamu do DB
            $postImageId = DB::table('post_images')->insertGetId([
                'post_id' => $request->post_id,
                'image' => $uploadData->url,
                'public_id' => $uploadData->public_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'post_image_id');

            Log::info('🧩 Image uploaded successfully', [
                'post_image_id' => $postImageId,
                'url' => $uploadData->url,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Image uploaded successfully.',
                'image' => [
                    'id' => $postImageId,
                    'url' => $uploadData->url,
                    'public_id' => $uploadData->public_id ?? null,
                ]
            ]);
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
