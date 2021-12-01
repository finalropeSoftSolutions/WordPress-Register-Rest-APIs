<?php 

/**
* Plugin Name: Finalrope Rest APIs
* Plugin URI: https://finalrope.com/
* Description: WordPress custom Rest APIs created by Finalrope Soft Solutions Pvt. Ltd.
* Version: 1.0
* Author: Ravikas
* Author URI: https://finalrope.com/
* License: A license under GPL12
*/


/***** Register APIs *****/
add_action( 'rest_api_init', function () {
  register_rest_route( 'register-apis/finalrope', 'register-call', array(
    'methods' => 'POST',
    'callback' => 'register_apis',
  ));
});
function register_apis($request = null) {
    
    /* check if a form was submitted */
    if( !empty( $_POST ) ){
    
        /* convert form data to json format */
        $postArray = array(
          "username" => $_POST['username'],
          "email" => $_POST['email'],
          "password" => $_POST['password']
        );
        $postArrayjson = json_encode( $postArray );
        
        /* make sure there were no problems
        //if( json_last_error() != JSON_ERROR_NONE ){
            //exit;  // do your error handling here instead of exiting
        // }
        $file = 'entries.json';
        // write to file
        //   note: _server_ path, NOT "web address (url)"!
        file_put_contents( $file, $json, FILE_APPEND);*/
    } 
    
    $parameters = json_decode($postArrayjson, true);
    print_r($someArray);

    $username = sanitize_text_field($parameters['username']);
    $email = sanitize_text_field($parameters['email']);
    $password = sanitize_text_field($parameters['password']);
    $error = new WP_Error();
    if (empty($username)) {
        $error->add(400, __("Username field 'username' is required.", 'wp-rest-user'), array('status' => 400));
        return $error;
    }
    if (empty($email)) {
        $error->add(401, __("Email field 'email' is required.", 'wp-rest-user'), array('status' => 400));
        return $error;
    }
    if (empty($password)) {
        $error->add(404, __("Password field 'password' is required.", 'wp-rest-user'), array('status' => 400));
        return $error;
    }
    $user_id = username_exists($username);
    if (!$user_id && email_exists($email) == false) {
        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            $user = get_user_by('id', $user_id);
            $user->set_role('subscriber');
            /* WooCommerce specific code */
            if (class_exists('WooCommerce')) {
                $user->set_role('customer');
            }
            /* Ger User Data (Non-Sensitive, Pass to front end.) */
            $response['status'] = "Registration was Successful";
            $response['ID'] = $user_id;
            $response['email'] = $email;
            $response['username'] = $username;
        } else {
            return $user_id;
        }
    } else {
        $error->add(406, __("Email already exists, please try 'Reset Password'", 'wp-rest-user'), array('status' => 400));
        return $error;
    }
    return new WP_REST_Response($response, 123);
}