# FileUpload Class Implementation

## Overview

The `FileUpload` class provides secure file upload handling for the admin management interface, specifically designed for image uploads (products, receipts, avatars).

## Location

`app/core/FileUpload.php`

## Features

### 1. Image Validation

**Method**: `validateImage(array $file): array`

Validates uploaded files against security and format requirements:
- Checks upload success status
- Validates file size (max 5MB)
- Validates MIME type using `finfo` (JPEG, PNG, GIF, WEBP)
- Returns array of error messages (empty if valid)

### 2. Unique Filename Generation

**Method**: `generateUniqueFilename(string $originalName): string`

Generates collision-resistant filenames:
- Format: `{timestamp}_{random16hex}.{extension}`
- Example: `1774875702_8aebc82b047b4c97.jpg`
- Preserves original file extension

### 3. Image Upload

**Method**: `uploadImage(array $file, string $directory): ?string`

Complete upload workflow:
1. Validates file using `validateImage()`
2. Creates directory if it doesn't exist (with 0755 permissions)
3. Generates unique filename
4. Moves uploaded file to destination
5. Returns filename on success, null on failure

### 4. File Deletion

**Method**: `deleteFile(string $path): bool`

Safely deletes files:
- Checks file existence before deletion
- Returns true on success, false if file doesn't exist

## Configuration

### Allowed Image Types
```php
private const ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
];
```

### Maximum File Size
```php
private const MAX_FILE_SIZE = 5242880; // 5MB in bytes
```

## Upload Directories

The following directories are available for file uploads:

1. **Products**: `public/uploads/products/`
   - Product images and galleries

2. **Receipts**: `public/uploads/receipts/`
   - Payment receipt images for bank transfer verification

3. **Avatars**: `public/uploads/avatars/`
   - User profile avatars

## Usage Examples

### Basic Image Upload

```php
require_once __DIR__ . '/app/core/FileUpload.php';
use App\Core\FileUpload;

// In controller
if (isset($_FILES['image'])) {
    $directory = __DIR__ . '/public/uploads/products/';
    $filename = FileUpload::uploadImage($_FILES['image'], $directory);
    
    if ($filename !== null) {
        // Success - save filename to database
        $imageUrl = '/uploads/products/' . $filename;
    } else {
        // Failed - show error
        $errors = FileUpload::validateImage($_FILES['image']);
        echo implode(', ', $errors);
    }
}
```

### Validation Before Upload

```php
// Validate first
$errors = FileUpload::validateImage($_FILES['image']);

if (empty($errors)) {
    // Proceed with upload
    $filename = FileUpload::uploadImage($_FILES['image'], $directory);
} else {
    // Display errors to user
    foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
    }
}
```

### Delete Old Image

```php
// Delete old image when updating
$oldImagePath = __DIR__ . '/public/uploads/products/old_image.jpg';
FileUpload::deleteFile($oldImagePath);
```

## Security Features

1. **MIME Type Validation**: Uses `finfo_file()` to check actual file content, not just extension
2. **File Size Limits**: Prevents large file uploads that could exhaust server resources
3. **Unique Filenames**: Prevents file overwrites and path traversal attacks
4. **Directory Isolation**: Files stored in specific directories by type
5. **Extension Preservation**: Maintains original file extension for proper handling

## Error Messages

The `validateImage()` method returns descriptive error messages:

- `"File upload failed"` - Upload error or missing file
- `"File size exceeds 5MB limit"` - File too large
- `"Invalid file type. Only JPEG, PNG, GIF, and WEBP are allowed"` - Wrong MIME type

## Requirements Satisfied

This implementation satisfies all acceptance criteria for Requirement 16:

- ✅ 16.1: Validates file types (JPEG, PNG, GIF, WEBP)
- ✅ 16.2: Validates 5MB size limit
- ✅ 16.3: Generates unique filenames
- ✅ 16.4: Organized directories (products, receipts, avatars)
- ✅ 16.5: Returns error messages on failure
- ✅ 16.6: Validates MIME types in addition to extensions

## Testing

Test files are available in `tests/`:
- `FileUploadTest.php` - Unit tests for individual methods
- `FileUploadIntegrationTest.php` - Integration tests for complete workflow

Run tests:
```bash
php tests/FileUploadTest.php
php tests/FileUploadIntegrationTest.php
```

## Future Enhancements

Potential improvements for future iterations:
- Image resizing/thumbnail generation
- Additional file type support (PDF for documents)
- Cloud storage integration (S3, Azure Blob)
- Virus scanning integration
- Image optimization (compression)
