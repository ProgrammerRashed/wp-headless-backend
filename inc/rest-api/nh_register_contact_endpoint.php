<?php


function nh_register_contact_endpoint()
{
    register_rest_route('nh/v1', '/cform/', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'nh_handle_contact_submission',
        'permission_callback' => '__return_true', // Publicly accessible
    ));
}
add_action('rest_api_init', 'nh_register_contact_endpoint');

/**
 * Handle contact form submission
 *
 * @param WP_REST_Request $request
 * @return WP_Error|array
 */

 function nh_handle_contact_submission(WP_REST_Request $request)
 {
     $params = $request->get_json_params();
 
     if (empty($params)) {
         return new WP_Error('no_data', 'No data received', array('status' => 400));
     }
     $admin_email = carbon_get_theme_option('contact_email');
 
 
     // Get the user's email
     $user_email = isset($params['email']) ? sanitize_email($params['email']) : '';
 
     // Get the other form fields
     $full_name = isset($params['fullName']) ? sanitize_text_field($params['fullName']) : '';
     $company_name = isset($params['company']) ? sanitize_text_field($params['company']) : '';
     $message = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';
     $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : '';
 
     // Prepare the message for the admin email
     $admin_subject = 'New Contact Form Submission';
     $admin_message = "You have a new contact form submission:\n\n" .
                      "Name: $full_name \n" .
                      "Email: $user_email\n" .
                      "Phone: $phone\n" .
                      "Company Name: $company_name\n" .
                      "Message: $message";
 
     // Prepare the message for the user email
     $user_subject = 'Thank you for contacting us';
     $user_message = "Hi $full_name,\n\n" .
                     "Thank you for reaching out. We have received your message and will get back to you soon.\n\n" .
                     "Here is a copy of your message:\n" .
                     "Company Name: $company_name\n" .
                     "Message: $message";
 
    // WP EMAIL HEADERS FOR ADMIN AND USER
    //HEADER FOR ADMIN 
    $admin_headers = array();
    if (!empty($user_email)) {
        $admin_headers[] = 'Reply-To: ' . $user_email;
    }
    //HEADER FOR USER 
    $user_headers = array();
    if (!empty($user_email)) {
        $user_headers[] = 'Reply-To: ' . $admin_email;
    }

     // Send email to the admin
     wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);
 
     // Send email to the user (if their email is provided)
     if (!empty($user_email)) {
         wp_mail($user_email, $user_subject, $user_message, $user_headers);
     }
 
     return new WP_REST_Response(array(
         'success' => true,
         'message' => 'Data received and emails sent successfully, Thanks for contacting us.',
         'data' => $params
     ), 200);
 }