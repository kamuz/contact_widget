<?php
// Check $_POST
if($_SERVER['REQUEST_METHOD'] == "POST"){
    // Get and sanitize $_POST values
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = strip_tags(trim($_POST['message']));
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];

    // Simple validation
    if(empty($name) || empty($email) || empty($message)){
        // Set a 400 (bad request) response code and exit
        http_response_code(400);
        echo "Please check your form fields.";
        exit;
    }

    // Build Message
    $text = "Name: $name\n";
    $text .= "Email: $email\n";
    $text .= "Message: $message\n";

    // Build Headers
    $headers = "From: $name <$email>";

    // Send Email
    if(mail($recipient, $subject, $text, $headers)){
        // Set 200 response (success)
        http_response_code(200);
        echo "Thank You: Your message has been send";
    } else{
        // Set 500 response (internal server message)
        http_response_code(500);
        echo "Error: There was a problem sending your message";
    }
} else{
    // Set 403 response (forbidden)
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}