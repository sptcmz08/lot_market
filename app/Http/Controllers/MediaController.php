<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $path)
    {
        abort_if(str_contains($path, '..'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}
