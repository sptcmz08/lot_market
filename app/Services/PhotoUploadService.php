<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoUploadService
{
    /**
     * Upload and compress image if possible, otherwise standard store.
     */
    public function upload(UploadedFile $file, string $folder = 'delivery-photos'): string
    {
        // Check if GD extension is loaded
        if (extension_loaded('gd')) {
            try {
                $imageInfo = getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $mime = $imageInfo['mime'];
                    
                    // Only process standard formats
                    if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                        // Load image
                        switch ($mime) {
                            case 'image/jpeg':
                                $srcImage = imagecreatefromjpeg($file->getRealPath());
                                break;
                            case 'image/png':
                                $srcImage = imagecreatefrompng($file->getRealPath());
                                break;
                            case 'image/webp':
                                $srcImage = imagecreatefromwebp($file->getRealPath());
                                break;
                            default:
                                $srcImage = false;
                        }

                        if ($srcImage !== false) {
                            $width = imagesx($srcImage);
                            $height = imagesy($srcImage);

                            // Resize if width > 1600px
                            $maxWidth = 1600;
                            if ($width > $maxWidth) {
                                $newWidth = $maxWidth;
                                $newHeight = floor($height * ($maxWidth / $width));
                                
                                $tmpImage = imagecreatetruecolor($newWidth, $newHeight);
                                
                                // Preserve transparency for png/webp
                                if ($mime == 'image/png' || $mime == 'image/webp') {
                                    imagealphablending($tmpImage, false);
                                    imagesavealpha($tmpImage, true);
                                }
                                
                                imagecopyresampled($tmpImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                imagedestroy($srcImage);
                                $srcImage = $tmpImage;
                            }

                            // Generate new unique name
                            $filename = uniqid('img_', true) . '.jpg';
                            $destinationPath = storage_path('app/public/' . $folder);
                            
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }

                            $fullPath = $destinationPath . '/' . $filename;
                            
                            // Save as JPG with 75% quality
                            imagejpeg($srcImage, $fullPath, 75);
                            imagedestroy($srcImage);

                            return $folder . '/' . $filename;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback to standard save on exception
            }
        }

        // Standard save fallback
        return $file->store($folder, 'public');
    }
}
