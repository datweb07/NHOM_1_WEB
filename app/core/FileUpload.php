<?php

namespace App\Core;

class FileUpload
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_FILE_SIZE = 5242880; 
    
    public static function validateImage(array $file): array
    {
        $errors = [];
        
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return $errors;
        }
        
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds 5MB limit';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES, true)) {
            $errors[] = 'Invalid file type. Only JPEG, PNG, GIF, and WEBP are allowed';
        }
        
        return $errors;
    }
    
    public static function generateUniqueFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }
    
    public static function uploadImage(array $file, string $directory): ?string
    {
        $errors = self::validateImage($file);
        if (!empty($errors)) {
            return null;
        }
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = self::generateUniqueFilename($file['name']);
        $destination = $directory . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        return null;
    }
    
    public static function deleteFile(string $path): bool
    {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}
