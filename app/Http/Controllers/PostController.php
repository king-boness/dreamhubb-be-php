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
            'images.*' => 'nullable|file|image|max:5120' // max 5 MB na obrázok
        ]);

        // ✅ Vytvorenie postu
        $postId = DB::table('posts')->insertGetId([
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
            'uploaded_images' => $uploadedImages
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
