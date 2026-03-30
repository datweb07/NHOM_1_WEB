# Implementation Plan: User Avatar Upload

## Overview

This feature has been **fully implemented** in the codebase. The implementation includes database schema, upload directory structure, frontend UI with client-side validation and preview, backend controller with file validation and storage, routing, and session-based feedback.

The tasks below focus on **testing and validation** of the existing implementation to ensure all requirements and correctness properties are satisfied.

## Tasks

- [ ] 1. Set up testing infrastructure
  - Create PHPUnit test configuration for the project
  - Install property-based testing library (eris/eris or giorgiosironi/eris)
  - Set up test database with nguoi_dung table
  - Create test fixtures for avatar files (valid JPG, PNG, invalid formats, oversized files)
  - _Requirements: All_

- [ ]* 2. Write property-based tests for file validation
  - [ ]* 2.1 Write property test for file size validation
    - **Property 2: File Size Validation**
    - **Validates: Requirements 2.2, 4.3**
    - Generate random file sizes from 0 to 5MB
    - Verify files > 2MB are rejected with error message
    - Verify files ≤ 2MB pass validation
  
  - [ ]* 2.2 Write property test for MIME type validation
    - **Property 3: MIME Type Validation**
    - **Validates: Requirements 2.3, 4.4, 10.2**
    - Generate files with various MIME types
    - Verify only image/jpeg, image/jpg, image/png are accepted
    - Verify rejection sets appropriate error message
  
  - [ ]* 2.3 Write property test for filename uniqueness
    - **Property 7: Unique Filename Generation**
    - **Validates: Requirements 5.2**
    - Simulate multiple uploads by same user at different times
    - Verify all generated filenames are unique
    - Verify filename pattern matches avatar_{userId}_{timestamp}.{extension}

- [ ]* 3. Write property-based tests for authentication and security
  - [ ]* 3.1 Write property test for authentication requirement
    - **Property 4: Authentication Requirement**
    - **Validates: Requirements 4.1, 9.1, 9.2**
    - Test upload requests with and without valid session
    - Verify unauthenticated requests redirect without processing
  
  - [ ]* 3.2 Write property test for POST method enforcement
    - **Property 5: POST Method Enforcement**
    - **Validates: Requirements 9.3, 9.4**
    - Test upload with GET, POST, PUT, DELETE methods
    - Verify only POST method processes upload

- [ ]* 4. Write property-based tests for file storage
  - [ ]* 4.1 Write property test for upload directory creation
    - **Property 6: Upload Directory Creation**
    - **Validates: Requirements 5.1**
    - Delete upload directory before test
    - Verify directory is created automatically on upload
  
  - [ ]* 4.2 Write property test for file storage success
    - **Property 8: File Storage Success**
    - **Validates: Requirements 5.3**
    - Upload valid avatar file
    - Verify file exists at expected path after upload
  
  - [ ]* 4.3 Write property test for avatar URL format
    - **Property 9: Avatar URL Format**
    - **Validates: Requirements 5.4**
    - Upload avatar and retrieve from database
    - Verify avatar_url matches pattern /public/uploads/avatars/avatar_{userId}_{timestamp}.{extension}

- [ ]* 5. Write property-based tests for avatar cleanup
  - [ ]* 5.1 Write property test for old avatar deletion
    - **Property 10: Old Avatar Cleanup**
    - **Validates: Requirements 6.1, 6.2**
    - Create user with existing avatar
    - Upload new avatar
    - Verify old avatar file is deleted from filesystem
  
  - [ ]* 5.2 Write property test for cleanup resilience
    - **Property 11: Cleanup Resilience**
    - **Validates: Requirements 6.3**
    - Simulate old avatar deletion failure
    - Verify upload still completes successfully
  
  - [ ]* 5.3 Write property test for safe deletion
    - **Property 12: Safe Deletion**
    - **Validates: Requirements 6.4**
    - Test deletion when old avatar file doesn't exist
    - Verify no errors occur

- [ ]* 6. Write property-based tests for database operations
  - [ ]* 6.1 Write property test for database round trip
    - **Property 13: Database Update Round Trip**
    - **Validates: Requirements 7.1**
    - Upload avatar
    - Query database for user's avatar_url
    - Verify returned value matches uploaded avatar path
  
  - [ ]* 6.2 Write property test for timestamp update
    - **Property 14: Timestamp Update**
    - **Validates: Requirements 7.2**
    - Record user's ngay_cap_nhat before upload
    - Upload avatar
    - Verify ngay_cap_nhat is updated to current datetime

- [ ]* 7. Write property-based tests for user feedback
  - [ ]* 7.1 Write property test for error message setting
    - **Property 15: Error Message Setting**
    - **Validates: Requirements 4.5, 5.5, 7.4, 10.5**
    - Trigger various validation failures
    - Verify $_SESSION['error'] is set with specific reason
  
  - [ ]* 7.2 Write property test for success message setting
    - **Property 16: Success Message Setting**
    - **Validates: Requirements 7.3**
    - Complete successful upload
    - Verify $_SESSION['success'] is set
  
  - [ ]* 7.3 Write property test for upload completion redirect
    - **Property 17: Upload Completion Redirect**
    - **Validates: Requirements 8.1**
    - Test both successful and failed uploads
    - Verify all redirect to /client/profile

- [ ]* 8. Write unit tests for avatar display
  - [ ]* 8.1 Write unit test for default avatar display
    - **Property 1: Default Avatar Display**
    - **Validates: Requirements 1.3**
    - Create user with null avatar_url
    - Render profile page
    - Verify default avatar path is displayed
  
  - [ ]* 8.2 Write unit test for custom avatar display
    - Test user with valid avatar_url
    - Verify custom avatar is displayed in sidebar (80x80px)
    - Verify custom avatar is displayed in preview (200x200px)
    - _Requirements: 1.1, 1.2_
  
  - [ ]* 8.3 Write unit test for session message cleanup
    - **Property 18: Session Message Cleanup**
    - **Validates: Requirements 8.5**
    - Set success/error message in session
    - Render profile page
    - Verify message is removed from session after display

- [ ]* 9. Write unit tests for specific validation scenarios
  - [ ]* 9.1 Write unit test for valid JPG upload
    - Upload valid JPG file
    - Verify successful storage and database update
    - _Requirements: 2.1, 4.4, 10.1_
  
  - [ ]* 9.2 Write unit test for valid PNG upload
    - Upload valid PNG file
    - Verify successful storage and database update
    - _Requirements: 2.1, 4.4, 10.1_
  
  - [ ]* 9.3 Write unit test for exactly 2MB file
    - Upload file with size exactly 2,097,152 bytes
    - Verify upload succeeds (boundary test)
    - _Requirements: 2.2, 4.3_
  
  - [ ]* 9.4 Write unit test for 2MB + 1 byte file
    - Upload file with size 2,097,153 bytes
    - Verify upload fails with size error message
    - _Requirements: 2.2, 4.3_
  
  - [ ]* 9.5 Write unit test for invalid MIME type
    - Upload GIF or other non-allowed format
    - Verify upload fails with format error message
    - _Requirements: 2.3, 4.4, 10.2_
  
  - [ ]* 9.6 Write unit test for missing file selection
    - Submit form without selecting file
    - Verify error message about missing file
    - _Requirements: 4.2_

- [ ]* 10. Write integration tests for complete upload flow
  - [ ]* 10.1 Write integration test for first-time avatar upload
    - Authenticate user with no existing avatar
    - Upload valid avatar file
    - Verify file stored on disk
    - Verify database updated with avatar_url
    - Verify success message displayed
    - Verify avatar displayed in UI
    - _Requirements: 1.1, 1.2, 4.1, 5.1, 5.2, 5.3, 5.4, 7.1, 7.2, 7.3, 8.1, 8.2_
  
  - [ ]* 10.2 Write integration test for avatar replacement
    - Authenticate user with existing avatar
    - Upload new avatar file
    - Verify old avatar file deleted
    - Verify new file stored on disk
    - Verify database updated with new avatar_url
    - Verify success message displayed
    - _Requirements: 6.1, 6.2, 7.1, 7.2, 7.3, 8.1_
  
  - [ ]* 10.3 Write integration test for upload failure scenarios
    - Test oversized file upload flow
    - Test invalid format upload flow
    - Test unauthenticated upload attempt
    - Verify appropriate error messages displayed
    - _Requirements: 2.2, 2.3, 4.2, 4.3, 4.4, 4.5, 8.1, 8.3, 9.1, 9.2_

- [ ]* 11. Write tests for client-side functionality
  - [ ]* 11.1 Write JavaScript test for client-side file size validation
    - Simulate file selection with oversized file
    - Verify alert is displayed
    - Verify file input is cleared
    - _Requirements: 2.2, 2.4_
  
  - [ ]* 11.2 Write JavaScript test for client-side format validation
    - Simulate file selection with invalid format
    - Verify alert is displayed
    - Verify file input is cleared
    - _Requirements: 2.3, 2.5_
  
  - [ ]* 11.3 Write JavaScript test for preview functionality
    - Simulate valid file selection
    - Verify FileReader is used
    - Verify preview image src is updated
    - _Requirements: 3.1, 3.2, 3.3_

- [ ] 12. Checkpoint - Ensure all tests pass
  - Run all property-based tests and unit tests
  - Verify all 18 correctness properties are validated
  - Ensure all tests pass, ask the user if questions arise

- [ ]* 13. Manual testing and validation
  - [ ]* 13.1 Perform manual testing of upload flow
    - Test uploading JPG, JPEG, and PNG files
    - Test uploading oversized file (should fail)
    - Test uploading invalid format (should fail)
    - Test avatar display in sidebar and preview sections
    - Test default avatar display for new users
    - Test client-side preview functionality
    - Verify error and success messages display correctly
    - _Requirements: All_
  
  - [ ]* 13.2 Perform security testing
    - Test upload without authentication (should redirect)
    - Test upload with GET request (should redirect)
    - Test concurrent uploads by same user
    - Verify no path traversal vulnerabilities
    - _Requirements: 9.1, 9.2, 9.3, 9.4_
  
  - [ ]* 13.3 Perform performance testing
    - Test upload with maximum allowed file size (2MB)
    - Measure upload time and processing time
    - Test behavior with many avatar files in directory
    - _Requirements: Performance considerations from design_

- [ ] 14. Final checkpoint - Review and documentation
  - Ensure all tests pass
  - Document any issues or limitations found
  - Ask the user if questions arise

## Notes

- All tasks marked with `*` are optional testing tasks
- The implementation is already complete - these tasks validate correctness
- Property-based tests validate universal properties across many inputs
- Unit tests validate specific examples and edge cases
- Integration tests validate end-to-end flows
- Manual testing provides final validation of user experience
- Each property test references its design document property number
- Each task references specific requirements for traceability
