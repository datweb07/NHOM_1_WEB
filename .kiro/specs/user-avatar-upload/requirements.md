# Requirements Document

## Introduction

The User Avatar Upload and Display feature enables authenticated users to personalize their profiles by uploading and displaying profile pictures (avatars). This feature enhances user experience by providing visual identity throughout the FPT Shop e-commerce application. Users can upload avatar images in common formats with size restrictions, preview their selection before upload, and see their avatar displayed in the profile page sidebar and header.

## Glossary

- **Avatar_Upload_System**: The subsystem responsible for handling avatar file uploads, validation, storage, and database updates
- **Profile_Display_System**: The subsystem responsible for rendering user avatars in the UI
- **User**: An authenticated customer with an active session
- **Avatar**: A profile picture image file associated with a user account
- **Avatar_File**: An uploaded image file in JPG, JPEG, or PNG format
- **Avatar_URL**: The file path stored in the database pointing to the avatar image location
- **Upload_Directory**: The server directory path `/public/uploads/avatars/` where avatar files are stored
- **File_Size_Limit**: Maximum allowed file size of 2 megabytes (2,097,152 bytes)
- **Allowed_Formats**: Image file formats JPG, JPEG, and PNG
- **Unique_Filename**: A generated filename following the pattern `avatar_{userId}_{timestamp}.{extension}`
- **Preview_Image**: A client-side display of the selected avatar before upload
- **Default_Avatar**: The placeholder image displayed when a user has no custom avatar
- **Session_Message**: Success or error feedback stored in the PHP session

## Requirements

### Requirement 1: Avatar Display

**User Story:** As a user, I want to see my current avatar on my profile page, so that I can verify my profile picture is displayed correctly.

#### Acceptance Criteria

1. WHEN a user accesses the profile page, THE Profile_Display_System SHALL display the user's Avatar in the sidebar header at 80x80 pixels
2. WHEN a user accesses the profile page, THE Profile_Display_System SHALL display the user's Avatar in the upload preview section at 200x200 pixels
3. WHEN a user has no Avatar_URL in the database, THE Profile_Display_System SHALL display the Default_Avatar image
4. THE Profile_Display_System SHALL render all avatars as circular images with object-fit cover styling
5. THE Profile_Display_System SHALL apply a border and shadow to avatar images for visual consistency

### Requirement 2: Avatar File Selection

**User Story:** As a user, I want to select an avatar image file from my device, so that I can upload a new profile picture.

#### Acceptance Criteria

1. THE Profile_Display_System SHALL provide a file input control that accepts only Allowed_Formats
2. WHEN a user selects an Avatar_File, THE Profile_Display_System SHALL validate the file size does not exceed the File_Size_Limit
3. WHEN a user selects an Avatar_File, THE Profile_Display_System SHALL validate the file format is one of the Allowed_Formats
4. IF the selected file exceeds the File_Size_Limit, THEN THE Profile_Display_System SHALL display an error alert and clear the file selection
5. IF the selected file format is not in Allowed_Formats, THEN THE Profile_Display_System SHALL display an error alert and clear the file selection

### Requirement 3: Client-Side Avatar Preview

**User Story:** As a user, I want to preview my selected avatar image before uploading, so that I can verify it looks correct.

#### Acceptance Criteria

1. WHEN a user selects a valid Avatar_File, THE Profile_Display_System SHALL display the Preview_Image in the avatar preview section
2. THE Profile_Display_System SHALL update the Preview_Image immediately after file selection without requiring form submission
3. THE Profile_Display_System SHALL use FileReader API to generate the Preview_Image from the selected file

### Requirement 4: Avatar Upload Processing

**User Story:** As a user, I want to upload my selected avatar image, so that it becomes my new profile picture.

#### Acceptance Criteria

1. WHEN a user submits the avatar upload form, THE Avatar_Upload_System SHALL verify the user has an active authenticated session
2. WHEN a user submits the avatar upload form without selecting a file, THE Avatar_Upload_System SHALL set an error Session_Message and redirect to the profile page
3. WHEN an Avatar_File is uploaded, THE Avatar_Upload_System SHALL validate the file size does not exceed the File_Size_Limit
4. WHEN an Avatar_File is uploaded, THE Avatar_Upload_System SHALL validate the MIME type is one of the Allowed_Formats using mime_content_type function
5. IF server-side validation fails, THEN THE Avatar_Upload_System SHALL set an error Session_Message with the specific validation failure reason

### Requirement 5: Avatar File Storage

**User Story:** As a user, I want my uploaded avatar to be stored securely, so that it persists across sessions.

#### Acceptance Criteria

1. WHEN storing an Avatar_File, THE Avatar_Upload_System SHALL create the Upload_Directory if it does not exist
2. WHEN storing an Avatar_File, THE Avatar_Upload_System SHALL generate a Unique_Filename using the user ID and current timestamp
3. THE Avatar_Upload_System SHALL move the uploaded file from the temporary location to the Upload_Directory with the Unique_Filename
4. WHEN an Avatar_File is successfully stored, THE Avatar_Upload_System SHALL construct the Avatar_URL as `/public/uploads/avatars/{Unique_Filename}`
5. IF the file move operation fails, THEN THE Avatar_Upload_System SHALL set an error Session_Message

### Requirement 6: Old Avatar Cleanup

**User Story:** As a user, I want my old avatar to be removed when I upload a new one, so that unused files don't accumulate on the server.

#### Acceptance Criteria

1. WHEN a new Avatar_File is successfully uploaded, THE Avatar_Upload_System SHALL retrieve the user's current Avatar_URL from the database
2. WHEN the user has an existing Avatar_URL, THE Avatar_Upload_System SHALL delete the old avatar file from the filesystem
3. THE Avatar_Upload_System SHALL continue processing even if the old avatar file deletion fails
4. THE Avatar_Upload_System SHALL only attempt to delete files that exist in the filesystem

### Requirement 7: Database Avatar Update

**User Story:** As a user, I want my new avatar to be associated with my account, so that it displays on my profile.

#### Acceptance Criteria

1. WHEN an Avatar_File is successfully stored, THE Avatar_Upload_System SHALL update the user's Avatar_URL in the nguoi_dung table
2. WHEN updating the Avatar_URL, THE Avatar_Upload_System SHALL also update the ngay_cap_nhat timestamp to the current datetime
3. WHEN the database update succeeds, THE Avatar_Upload_System SHALL set a success Session_Message
4. IF the database update fails, THEN THE Avatar_Upload_System SHALL set an error Session_Message

### Requirement 8: User Feedback

**User Story:** As a user, I want to receive clear feedback about my avatar upload, so that I know whether it succeeded or failed.

#### Acceptance Criteria

1. WHEN the avatar upload completes, THE Avatar_Upload_System SHALL redirect the user to the profile page
2. WHEN the avatar upload succeeds, THE Profile_Display_System SHALL display a success alert with the message from the Session_Message
3. WHEN the avatar upload fails, THE Profile_Display_System SHALL display an error alert with the specific failure reason from the Session_Message
4. THE Profile_Display_System SHALL provide a dismiss button on all alert messages
5. THE Profile_Display_System SHALL clear the Session_Message after displaying it

### Requirement 9: Security and Access Control

**User Story:** As a user, I want only authenticated users to upload avatars, so that the system remains secure.

#### Acceptance Criteria

1. WHEN processing an avatar upload request, THE Avatar_Upload_System SHALL verify the user has an active session with a valid user_id
2. IF the user is not authenticated, THEN THE Avatar_Upload_System SHALL redirect to the profile page without processing the upload
3. WHEN processing an avatar upload request, THE Avatar_Upload_System SHALL verify the HTTP method is POST
4. IF the HTTP method is not POST, THEN THE Avatar_Upload_System SHALL redirect to the profile page without processing the upload

### Requirement 10: File Format Validation

**User Story:** As a system administrator, I want to restrict avatar uploads to safe image formats, so that security risks are minimized.

#### Acceptance Criteria

1. THE Avatar_Upload_System SHALL validate uploaded files using MIME type detection via mime_content_type function
2. THE Avatar_Upload_System SHALL reject files with MIME types other than image/jpeg, image/jpg, and image/png
3. THE Profile_Display_System SHALL configure the file input to accept only image/jpeg, image/jpg, and image/png MIME types
4. WHEN a disallowed file type is detected on the client, THE Profile_Display_System SHALL display an error alert specifying the Allowed_Formats
5. WHEN a disallowed file type is detected on the server, THE Avatar_Upload_System SHALL set an error Session_Message specifying the Allowed_Formats
