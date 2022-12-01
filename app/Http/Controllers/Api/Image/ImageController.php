<?php

namespace App\Http\Controllers\Api\Image;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $images = $request->file('image') ?? $request->file('images');
        $path = [];
        if (is_array($images)) {
            foreach ($images as $image) {
                $path[] = asset(str_replace('public', 'storage', $image->store('/public/images')));
            }
        }

        return $path;
    }
}
