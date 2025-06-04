<?php

function nh_register_job_apply_endpoint()
{
    register_rest_route('nh/v1', '/apply/', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'nh_handle_job_apply_submission',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'nh_register_job_apply_endpoint');

/**
 * Handle job application submission (including both resume and cover letter)
 *
 * @param WP_REST_Request $request
 * @return WP_Error|array
 */
function nh_handle_job_apply_submission(WP_REST_Request $request) {
    // Retrieve non-file parameters
    $firstName = $request->get_param('firstName');
    $lastName = $request->get_param('lastName');
    $email = $request->get_param('email');
    $phone = $request->get_param('phone');
    $job_title = $request->get_param('job_title');

    // Retrieve file parameters
    $files = $request->get_file_params();

    // Process the resume file
    $resumeFile = isset($files['resume']) ? $files['resume'] : null;
    if ($resumeFile) {
        $resumeResponse = handle_uploaded_file($resumeFile, 'resume');
        if (is_wp_error($resumeResponse)) {
            return $resumeResponse;
        }
        $resumeFilePath = $resumeResponse;
    } else {
        return new WP_REST_Response([
            'message' => 'Resume is required.',
        ], 400);
    }

    // Process the cover letter file (if exists)
    $coverLetterFile = isset($files['coverLetter']) ? $files['coverLetter'] : null;
    $coverLetterFilePath = null;
    if ($coverLetterFile) {
        $coverLetterResponse = handle_uploaded_file($coverLetterFile, 'coverLetter');
        if (is_wp_error($coverLetterResponse)) {
            return $coverLetterResponse;
        }
        $coverLetterFilePath = $coverLetterResponse;
    }

    // Prepare email details
    $to = 'saf.serverinfo@gmail.com'; // Admin's email address
    $subject = !empty($job_title) ? "New Job Application for $job_title" : "New Job Application received";
    $body = !empty($job_title) ? "A new job application has been submitted for :$job_title position\n\n" : "A new job application has been submitted\n";
    $body .= "First Name: $firstName\n";
    $body .= "Last Name: $lastName\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n";

    // Attach resume and cover letter (if provided)
    $attachments = [$resumeFilePath];
    if ($coverLetterFilePath) {
        $attachments[] = $coverLetterFilePath;
    }

        // Admin headers: Reply-To should be the applicant's email
    $admin_headers = array();
    if (!empty($email)) {
        $admin_headers[] = 'Reply-To: ' . $email;
    }

    // User headers: Reply-To should be the admin's email
    $user_headers = array();
    if (!empty($admin_email)) {
        $user_headers[] = 'Reply-To: ' . $admin_email;
    }


    // Send the email to the admin
    $sent = wp_mail($admin_email, $subject, $body, $admin_headers, $attachments);

    // Send confirmation email to the user
    $user_email_subject = !empty($job_title) ? "Application received for $job_title position" : "Application received";
    $user_message = "Thank you for your application. We will get back to you soon.";
   // Send confirmation email to the user
    wp_mail($email, $user_email_subject, $user_message, $user_headers);

    // Remove temporary files after sending the email
    if ($sent) {
        unlink($resumeFilePath);
        if ($coverLetterFilePath) {
            unlink($coverLetterFilePath);
        }
    } else {
        return new WP_REST_Response([
            'message' => 'Failed to send the email. Please try again later.',
        ], 500);
    }

    // Return success response
    return new WP_REST_Response([
        'message' => 'Job application submitted successfully.',
    ], 200);
}

/**
 * Handle file upload and validation (for resume and cover letter)
 *
 * @param array $file
 * @param string $fileType ('resume' or 'coverLetter')
 * @return string|WP_Error
 */
function handle_uploaded_file($file, $fileType) {
    // Validate file type (only allow PDFs)
    $allowed_mime_types = ['application/pdf'];
    if (!in_array($file['type'], $allowed_mime_types)) {
        return new WP_REST_Response([
            'message' => "Invalid file type. Only PDF files are allowed for $fileType.",
        ], 400);
    }

    // Validate file size (limit to 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        return new WP_REST_Response([
            'message' => "File size exceeds the maximum allowed limit of 5MB for $fileType.",
        ], 400);
    }

    // Get the original file name and ensure it has a .pdf extension
    $originalFileName = pathinfo(sanitize_file_name($file['name']), PATHINFO_FILENAME);
    $newFileName = $originalFileName . '.pdf'; // Ensure it has the .pdf extension

    // Define a temporary upload path
    $upload_dir = wp_upload_dir();
    $tempFilePath = $upload_dir['path'] . '/' . $newFileName;

    // Move the uploaded file to the temporary directory with a proper name
    if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {
        return new WP_REST_Response([
            'message' => "Failed to process the uploaded $fileType file.",
        ], 500);
    }

    // Return the file path for attachment
    return $tempFilePath;
}
