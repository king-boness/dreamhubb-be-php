<?php

namespace App\Http\Controllers;

use App\Models\PostImage;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public static function uploadImage($imageFile, $post_id)
    {
        $uploadFolder = 'users/' . Auth::user()->email . '/post_images';
        $imageName = $imageFile->getClientOriginalName();
        $result = $imageFile->storeOnCloudinaryAs($uploadFolder, $imageName);
        PostImage::create([
            'post_id' => $post_id,
            'image' => $result->getSecurePath(),
            'public_id' => $result->getPublicId(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public static function destroyPostImage($public_id)
    {
        Cloudinary::destroy($public_id);
    }
}
