<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    /**
     * 🟢 1️⃣ Získaj údaje o prihlásenom používateľovi (JWT)
     */
    public function me(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $guard = \Illuminate\Support\Facades\Auth::getDefaultDriver();

            $userFromAuth = \Illuminate\Support\Facades\Auth::user();
            $userFromGuard = auth('api')->user();

            return response()->json([
                'debug' => [
                    'token_present' => $token ? true : false,
                    'token_start' => $token ? substr($token, 0, 20) . '...' : null,
                    'guard' => $guard,
                    'user_from_Auth' => $userFromAuth,
                    'user_from_guard_api' => $userFromGuard,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🟢 2️⃣ Aktualizuj profil používateľa
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'username' => 'nullable|string|max:50',
                'date_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'location_city_id' => 'nullable|integer',
            ]);

            $user->update($request->only([
                'username', 'date_birth', 'gender', 'location_city_id'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully.',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🟢 3️⃣ Upload profilovej fotky – centralizovaný upload systém
     */
    public function uploadProfilePicture(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            $user = Auth::user();

            // 🔹 Zavoláme náš centrálny UploadController
            $uploadController = new UploadController();
            $uploadResponse = $uploadController->upload($request);
            $uploadData = $uploadResponse->getData();

            if ($uploadData->status !== 'success') {
                return $uploadResponse; // ak zlyhá, vráti priamo odpoveď UploadControlleru
            }

            // 🔹 Zmazanie starej profilovky (Cloudinary)
            if ($user->profile_picture_public_id && config('filesystems.default') === 'cloudinary') {
                try {
                    \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::destroy($user->profile_picture_public_id);
                } catch (Exception $e) {
                    // Ignoruj, ak sa nepodarí zmazať
                }
            }

            // 🔹 Aktualizácia DB
            $user->update([
                'profile_picture' => $uploadData->url,
                'profile_picture_public_id' => $uploadData->public_id ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully.',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload profile picture.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔴 3️⃣b Delete profilovej fotky
     */
    public function deleteProfilePicture(Request $request)
    {
        try {
            $user = Auth::user();
            $driver = config('filesystems.default');

            if (empty($user->profile_picture)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not have a profile picture to delete.'
                ], 400);
            }

            if ($driver === 'cloudinary' && !empty($user->profile_picture_public_id)) {
                try {
                    \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::destroy($user->profile_picture_public_id);
                } catch (\Exception $e) {}
            } else {
                $path = str_replace('/storage/', 'public/', $user->profile_picture);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            $user->update([
                'profile_picture' => null,
                'profile_picture_public_id' => null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture deleted successfully.',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete profile picture.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🟢 4️⃣ Zmena hesla
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Incorrect current password.'
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to change password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
