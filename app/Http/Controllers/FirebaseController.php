<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function imageUpload(Request $request)
    {
        $image = $request->profile_picture; //base64 string from frontend
        $student = app('firebase.firestore')->database()->collection('user_profile_image')->document('defT5uT7SDu9K5RFtIdl');
        $firebase_storage_path = 'user_profile_image/';
        $name = $student->id();
        $localfolder = public_path('firebase-temp-uploads') . '/';
        if (!file_exists($localfolder)) {
            mkdir($localfolder, 0777, true);
        }
        $parts = explode(";base64,", $image);
        $type_aux = explode("image/", $parts[0]);
        // $type = $aux[1];
        $base64 = base64_decode($parts[1]);
        $file = $name . '.png';
        if (file_put_contents($localfolder . $file, $base64)) {
            $uploadedfile = fopen($localfolder . $file, 'r');
            app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $firebase_storage_path . $name]);
            //will remove from local laravel folder
            unlink($localfolder . $file);
            echo 'success';
        } else {
            echo 'error';
        }
    }
}
