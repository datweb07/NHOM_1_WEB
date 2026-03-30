<?php

namespace App\Core;

class FileUpload
{
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_FILE_SIZE = 5242880; // 5MB in bytes
    
    /**
     * Validate uploaded image file
     * 
     * @param array $file The $_FILES array element
     * @return array Array of error messages (empty if valid)
     */
    public static function validateImage(array $file): array
    {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds 5MB limit';
        }
        
        // Check MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES, true)) {
            $errors[] = 'Invalid file type. Only JPEG, PNG, GIF, and WEBP are allowed';
        }
        
        return $errors;
    }
    
    /**
     * Generate unique filename with timestamp and random string
     * 
     * @param string $originalName Original filename
     * @return string Unique filename
     */
    public static function generateUniqueFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * Upload image file to specified directory
     * 
     * @param array $file The $_FILES array element
     * @param string $directory Target directory path
     * @return string|null Filename on success, null on failure
     */
    public static function uploadImage(array $file, string $directory): ?string
    {
        // Validate
        $errors = self::validateImage($file);
        if (!empty($errors)) {
            return null;
        }
        
        // Ensure directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate unique filename
        $filename = self::generateUniqueFilename($file['name']);
        $destination = $directory . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        
        return null;
    }
    
    /**
     * Delete file from filesystem
     * 
     * @param string $path Full path to file
     * @return bool True on success, false on failure
     */
    public static function deleteFile(string $path): bool
    {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}
