# Design Document: User Avatar Upload

## Overview

The User Avatar Upload feature enables authenticated users to upload, store, and display profile pictures within the FPT Shop e-commerce application. The system consists of two primary subsystems: the Avatar_Upload_System (server-side) and the Profile_Display_System (client-side). 

The Avatar_Upload_System handles file validation, storage, database updates, and cleanup of old avatar files. It enforces security through session-based authentication, validates file size (2MB limit) and format (JPG, JPEG, PNG), generates unique filenames to prevent collisions, and manages the complete upload lifecycle including error handling.

The Profile_Display_System provides the user interface for avatar selection, client-side validation, live preview using the FileReader API, and display of avatars throughout the profile page. It renders avatars in two locations: the sidebar header (80x80px) and the upload preview section (200x200px), both with circular styling.

The feature integrates with the existing authentication system, uses the `nguoi_dung` table's `avatar_url` column for persistence, stores files in `/public/uploads/avatars/`, and provides user feedback through PHP session messages.

## Architecture

### System Components

The avatar upload feature follows a traditional MVC architecture with clear separation between client and server responsibilities:

**Client-Side (Profile_Display_System)**
- View: `app/views/client/khach_hang/profile.php`
- JavaScript: Inline client-side validation and preview logic
- Responsibilities: File selection UI, client-side validation, live preview, avatar display

**Server-Side (Avatar_Upload_System)**
- Controller: `app/controllers/client/KhachHangController.php` (method: `capNhatAvatar()`)
- Model: `app/models/BaseModel.php` (table: `nguoi_dung`)
- Middleware: `app/middleware/AuthMiddleware.php`
- Responsibilities: Authentication, server-side validation, file storage, database updates, cleanup

**Routing**
- Route definition: `app/routes/client/client.php`
- Endpoint: `POST /khach-hang/cap-nhat-avatar`

### Data Flow

1. **Display Flow**: User accesses profile → AuthMiddleware validates session → Controller fetches user data → View renders avatar from `avatar_url` or default
2. **Upload Flow**: User selects file → Client validates → User submits → Server validates → File stored → Database updated → Old file deleted → User redirected with feedback

### Security Model

- Session-based authentication required for all upload operations
- POST method enforcement to prevent CSRF
- MIME type validation using `mime_content_type()` function
- File size validation (2MB maximum)
- Unique filename generation to prevent path traversal attacks
- Files stored in public directory with controlled naming convention

## Components and Interfaces

### Avatar_Upload_System

**KhachHangController::capNhatAvatar()**

```php
public function capNhatAvatar(): void
```

Primary upload handler that orchestrates the complete upload process.

**Responsibilities:**
- Session validation
- HTTP method verification
- File upload validation (existence, size, MIME type)
- Directory creation
- Unique filename generation
- File storage
- Old avatar cleanup
- Database update
- User feedback via session messages
- Redirection

**Dependencies:**
- `\App\Core\Session` - Session management
- `BaseModel('nguoi_dung')` - Database operations
- PHP file system functions
- `mime_content_type()` - MIME type detection

**Error Handling:**
- Missing file: "Vui lòng chọn ảnh để upload!"
- File too large: "Kích thước ảnh không được vượt quá 2MB!"
- Invalid format: "Chỉ chấp nhận file JPG, JPEG hoặc PNG!"
- Upload failure: "Upload ảnh thất bại!"
- Database failure: "Cập nhật ảnh đại diện thất bại!"

**Success Response:**
- Success message: "Cập nhật ảnh đại diện thành công!"
- Redirect to: `/client/profile`

### Profile_Display_System

**Avatar Display Logic**

The view renders avatars using a ternary expression:

```php
<?= !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : '/public/assets/client/images/others/anh-avatar.jpg' ?>
```

**Display Locations:**
1. Sidebar header: 80x80px circular avatar with border and shadow
2. Upload preview: 200x200px circular avatar with border and shadow

**Client-Side Validation**

JavaScript event listener on file input:
- Validates file size ≤ 2MB
- Validates file type in ['image/jpeg', 'image/jpg', 'image/png']
- Displays alert and clears selection on validation failure
- Generates live preview using FileReader API

**Upload Form**

```html
<form action="/khach-hang/cap-nhat-avatar" method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" accept="image/jpeg,image/jpg,image/png" required>
    <button type="submit">Cập nhật ảnh đại diện</button>
</form>
```

### Routing Interface

**Route Definition** (`app/routes/client/client.php`):

```php
if ($path === 'khach-hang/cap-nhat-avatar') {
    require_once dirname(__DIR__, 2) . '/controllers/client/KhachHangController.php';
    $controller = new KhachHangController();
    $controller->capNhatAvatar();
    return;
}
```

## Data Models

### Database Schema

**Table:** `nguoi_dung`

Relevant columns for avatar feature:
- `id` INT PRIMARY KEY - User identifier
- `avatar_url` VARCHAR(500) - File path to avatar image (nullable)
- `ngay_cap_nhat` DATETIME - Last update timestamp (auto-updated)

**Avatar URL Format:**
```
/public/uploads/avatars/avatar_{userId}_{timestamp}.{extension}
```

**Example:**
```
/public/uploads/avatars/avatar_42_1709123456.jpg
```

### File System Structure

**Upload Directory:** `/public/uploads/avatars/`

**Directory Creation:**
- Created automatically if not exists
- Permissions: 0777 (recursive)
- Location: `{project_root}/public/uploads/avatars/`

**Filename Pattern:**
```
avatar_{userId}_{timestamp}.{extension}
```

Components:
- `userId`: Integer user ID from session
- `timestamp`: Unix timestamp from `time()`
- `extension`: Original file extension (jpg, jpeg, png)

**File Constraints:**
- Maximum size: 2,097,152 bytes (2MB)
- Allowed MIME types: image/jpeg, image/jpg, image/png
- Allowed extensions: .jpg, .jpeg, .png

### Session Data

**Session Variables:**
- `$_SESSION['user_id']` - Authenticated user ID
- `$_SESSION['success']` - Success feedback message
- `$_SESSION['error']` - Error feedback message

**Message Lifecycle:**
1. Set by controller after operation
2. Read and displayed by view
3. Cleared immediately after display using `unset()`

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Default Avatar Display

*For any* user record with a null or empty `avatar_url` field, the Profile_Display_System should return the default avatar path `/public/assets/client/images/others/anh-avatar.jpg`

**Validates: Requirements 1.3**

### Property 2: File Size Validation

*For any* uploaded file, if the file size exceeds 2,097,152 bytes (2MB), the Avatar_Upload_System should reject the upload and set an error session message

**Validates: Requirements 2.2, 4.3**

### Property 3: MIME Type Validation

*For any* uploaded file, if the MIME type is not one of ['image/jpeg', 'image/jpg', 'image/png'], the Avatar_Upload_System should reject the upload and set an error session message

**Validates: Requirements 2.3, 4.4, 10.2**

### Property 4: Authentication Requirement

*For any* avatar upload request, if the session does not contain a valid `user_id`, the Avatar_Upload_System should redirect to the profile page without processing the upload

**Validates: Requirements 4.1, 9.1, 9.2**

### Property 5: POST Method Enforcement

*For any* avatar upload request, if the HTTP method is not POST, the Avatar_Upload_System should redirect to the profile page without processing the upload

**Validates: Requirements 9.3, 9.4**

### Property 6: Upload Directory Creation

*For any* avatar upload, if the upload directory `/public/uploads/avatars/` does not exist, the Avatar_Upload_System should create it before storing the file

**Validates: Requirements 5.1**

### Property 7: Unique Filename Generation

*For any* two avatar uploads, even by the same user at different times, the generated filenames should be different due to the timestamp component

**Validates: Requirements 5.2**

### Property 8: File Storage Success

*For any* successful avatar upload, the uploaded file should exist at the path `/public/uploads/avatars/{unique_filename}` after the operation completes

**Validates: Requirements 5.3**

### Property 9: Avatar URL Format

*For any* successfully stored avatar file, the `avatar_url` value should match the pattern `/public/uploads/avatars/avatar_{userId}_{timestamp}.{extension}`

**Validates: Requirements 5.4**

### Property 10: Old Avatar Cleanup

*For any* user with an existing non-null `avatar_url`, when a new avatar is successfully uploaded, the old avatar file should be deleted from the filesystem

**Validates: Requirements 6.1, 6.2**

### Property 11: Cleanup Resilience

*For any* avatar upload, if the old avatar file deletion fails, the upload operation should still complete successfully and update the database

**Validates: Requirements 6.3**

### Property 12: Safe Deletion

*For any* old avatar deletion attempt, the system should only attempt to delete the file if it exists in the filesystem

**Validates: Requirements 6.4**

### Property 13: Database Update Round Trip

*For any* successful avatar upload, querying the `nguoi_dung` table for the user's `avatar_url` should return the newly uploaded avatar's path

**Validates: Requirements 7.1**

### Property 14: Timestamp Update

*For any* avatar upload that updates the database, the `ngay_cap_nhat` field should be updated to the current datetime

**Validates: Requirements 7.2**

### Property 15: Error Message Setting

*For any* validation failure (file size, MIME type, missing file), the Avatar_Upload_System should set an error message in `$_SESSION['error']` with a specific failure reason

**Validates: Requirements 4.5, 5.5, 7.4, 10.5**

### Property 16: Success Message Setting

*For any* successful avatar upload and database update, the Avatar_Upload_System should set a success message in `$_SESSION['success']`

**Validates: Requirements 7.3**

### Property 17: Upload Completion Redirect

*For any* avatar upload request (success or failure), the Avatar_Upload_System should redirect the user to `/client/profile`

**Validates: Requirements 8.1**

### Property 18: Session Message Cleanup

*For any* session message displayed in the view, the message should be removed from the session immediately after display using `unset()`

**Validates: Requirements 8.5**

## Error Handling

### Validation Errors

**File Not Selected**
- Trigger: `$_FILES['avatar']['error'] !== UPLOAD_ERR_OK`
- Response: Set error message, redirect to profile
- User sees: "Vui lòng chọn ảnh để upload!"

**File Too Large**
- Trigger: `$file['size'] > 2 * 1024 * 1024`
- Response: Set error message, redirect to profile
- User sees: "Kích thước ảnh không được vượt quá 2MB!"

**Invalid File Format**
- Trigger: `!in_array(mime_content_type($file['tmp_name']), $allowedTypes)`
- Response: Set error message, redirect to profile
- User sees: "Chỉ chấp nhận file JPG, JPEG hoặc PNG!"

### System Errors

**Upload Failure**
- Trigger: `move_uploaded_file()` returns false
- Response: Set error message, redirect to profile
- User sees: "Upload ảnh thất bại!"
- File system state: Temporary file cleaned up by PHP

**Database Update Failure**
- Trigger: `$this->khachHangModel->update()` returns false
- Response: Set error message, redirect to profile
- User sees: "Cập nhật ảnh đại diện thất bại!"
- File system state: New file remains on disk (orphaned)

**Old Avatar Deletion Failure**
- Trigger: `@unlink()` fails (file doesn't exist or permission issue)
- Response: Continue processing (silent failure with @ suppression)
- User sees: Success message (upload succeeded)
- File system state: Old file remains on disk (orphaned)

### Security Errors

**Unauthenticated Request**
- Trigger: `$_SESSION['user_id']` is null or not set
- Response: Redirect to profile without processing
- User sees: No message (silent redirect)

**Non-POST Request**
- Trigger: `$_SERVER['REQUEST_METHOD'] !== 'POST'`
- Response: Redirect to profile without processing
- User sees: No message (silent redirect)

### Error Recovery

**Orphaned Files**
- Cause: Database update failure or old avatar deletion failure
- Impact: Disk space accumulation over time
- Mitigation: Implement periodic cleanup job (not in current scope)

**Missing Upload Directory**
- Cause: Directory doesn't exist or was deleted
- Recovery: Automatic creation with `mkdir($uploadDir, 0777, true)`

**Concurrent Uploads**
- Cause: Same user uploads multiple times simultaneously
- Protection: Timestamp-based unique filenames prevent collisions
- Result: Last upload wins, previous uploads become orphaned

## Testing Strategy

### Dual Testing Approach

This feature requires both unit tests and property-based tests for comprehensive coverage:

**Unit Tests** focus on:
- Specific examples of valid uploads (JPG, JPEG, PNG files)
- Edge cases (empty file, exactly 2MB file, 2MB + 1 byte file)
- Error conditions (missing session, non-POST request, invalid MIME type)
- Integration points (database updates, file system operations)
- Default avatar display when `avatar_url` is null

**Property-Based Tests** focus on:
- Universal properties that hold for all inputs
- File validation across randomly generated file sizes and types
- Filename uniqueness across multiple uploads
- Database round-trip consistency
- Error handling across various failure scenarios

### Property-Based Testing Configuration

**Library:** Use PHPUnit with a property-based testing extension such as `eris/eris` or `giorgiosironi/eris`

**Test Configuration:**
- Minimum 100 iterations per property test
- Each test must reference its design document property
- Tag format: `@group Feature: user-avatar-upload, Property {number}: {property_text}`

**Example Property Test Structure:**

```php
/**
 * @group Feature: user-avatar-upload, Property 2: File Size Validation
 */
public function testFileSizeValidation()
{
    $this->forAll(
        Generator\choose(0, 5 * 1024 * 1024) // 0 to 5MB
    )->then(function ($fileSize) {
        $result = $this->uploadSystem->validateFileSize($fileSize);
        
        if ($fileSize > 2 * 1024 * 1024) {
            $this->assertFalse($result);
            $this->assertNotEmpty($_SESSION['error']);
        } else {
            $this->assertTrue($result);
        }
    });
}
```

### Unit Test Coverage

**Authentication Tests:**
- Test upload with valid session
- Test upload without session (should redirect)
- Test upload with invalid user_id

**Validation Tests:**
- Test upload with valid JPG file
- Test upload with valid PNG file
- Test upload with 2MB file (boundary)
- Test upload with 2MB + 1 byte file (should fail)
- Test upload with invalid MIME type (e.g., image/gif)
- Test upload without file selection

**File Storage Tests:**
- Test directory creation when it doesn't exist
- Test filename generation format
- Test file exists after successful upload
- Test avatar URL format in database

**Cleanup Tests:**
- Test old avatar deletion when user has existing avatar
- Test upload succeeds even if old avatar deletion fails
- Test no deletion attempt when user has no existing avatar

**Database Tests:**
- Test avatar_url is updated after successful upload
- Test ngay_cap_nhat is updated after successful upload
- Test database rollback on upload failure

**Feedback Tests:**
- Test success message is set on successful upload
- Test error message is set on validation failure
- Test session message is cleared after display

### Integration Testing

**End-to-End Upload Flow:**
1. Authenticate user
2. Select valid avatar file
3. Submit upload form
4. Verify file stored on disk
5. Verify database updated
6. Verify old avatar deleted
7. Verify success message displayed
8. Verify avatar displayed in UI

**Error Flow Testing:**
1. Submit oversized file → verify error message
2. Submit invalid format → verify error message
3. Submit without authentication → verify redirect
4. Simulate database failure → verify error handling
5. Simulate file system failure → verify error handling

### Manual Testing Checklist

- [ ] Upload JPG avatar and verify display
- [ ] Upload PNG avatar and verify display
- [ ] Upload JPEG avatar and verify display
- [ ] Attempt to upload 3MB file (should fail)
- [ ] Attempt to upload GIF file (should fail)
- [ ] Upload avatar, then upload another (verify old deleted)
- [ ] Verify avatar displays in sidebar (80x80px)
- [ ] Verify avatar displays in preview (200x200px)
- [ ] Verify default avatar shows for new users
- [ ] Verify client-side preview works
- [ ] Verify error messages display correctly
- [ ] Verify success message displays correctly
- [ ] Test upload without authentication (should redirect)
- [ ] Test upload with GET request (should redirect)

### Performance Considerations

**File Upload Performance:**
- 2MB file upload time: ~1-2 seconds on typical connection
- MIME type detection: <10ms
- File move operation: <50ms
- Database update: <20ms

**Optimization Opportunities:**
- Consider image resizing/compression (not in current scope)
- Consider CDN storage for avatars (not in current scope)
- Consider async upload with progress bar (not in current scope)

**Load Testing:**
- Test concurrent uploads by multiple users
- Test behavior with 1000+ avatar files in directory
- Test database performance with large user base
