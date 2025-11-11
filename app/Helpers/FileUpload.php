<?php

namespace App\Helpers;

class FileUpload
{
    /**
     * Handle listing image uploads
     */
    public static function handleListingImages($listingId, $files)
    {
        $imageModel = new \App\Models\ListingImage();
        $uploadedImages = [];
        
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $originalName = $files['name'][$i];
                
                // Validate file
                if (self::validateImage($tmpName, $files['size'][$i])) {
                    $savedPath = self::saveImage($tmpName, $originalName, $listingId);
                    
                    if ($savedPath) {
                        $imageModel->create([
                            'listing_id' => $listingId,
                            'filename' => basename($savedPath),
                            'path' => $savedPath,
                            'size' => $files['size'][$i],
                            'sort_order' => $i
                        ]);
                        
                        $uploadedImages[] = $savedPath;
                    }
                }
            }
        }
        
        return $uploadedImages;
    }

    /**
     * Validate image file
     */
    private static function validateImage($tmpName, $size)
    {
        // Check file size
        if ($size > MAX_UPLOAD_SIZE) {
            return false;
        }
        
        // Check mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        
        return in_array($mimeType, ALLOWED_IMAGE_TYPES);
    }

    /**
     * Save image file
     */
    private static function saveImage($tmpName, $originalName, $listingId)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . $listingId . '.' . $extension;
        
        // Create directory structure: uploads/YYYY/MM/
        $uploadDir = UPLOAD_PATH . '/' . date('Y') . '/' . date('m');
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $destination = $uploadDir . '/' . $filename;
        $relativePath = '/uploads/' . date('Y') . '/' . date('m') . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($tmpName, $destination)) {
            // Create thumbnail
            self::createThumbnail($destination);
            return $relativePath;
        }
        
        return false;
    }

    /**
     * Create thumbnail
     */
    private static function createThumbnail($imagePath, $width = 300, $height = 300)
    {
        $info = getimagesize($imagePath);
        
        if (!$info) {
            return false;
        }
        
        list($origWidth, $origHeight, $type) = $info;
        
        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($imagePath);
                break;
            default:
                return false;
        }
        
        // Calculate dimensions
        $ratio = min($width / $origWidth, $height / $origHeight);
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);
        
        // Create thumbnail
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        // Save thumbnail
        $thumbPath = str_replace('.', '_thumb.', $imagePath);
        
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail, $thumbPath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $thumbPath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbnail, $thumbPath);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($thumbnail);
        
        return true;
    }

    /**
     * Delete image and its thumbnail
     */
    public static function deleteImage($imagePath)
    {
        $fullPath = PUBLIC_PATH . $imagePath;
        
        if (file_exists($fullPath)) {
            unlink($fullPath);
            
            // Delete thumbnail
            $thumbPath = str_replace('.', '_thumb.', $fullPath);
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
            
            return true;
        }
        
        return false;
    }
}

