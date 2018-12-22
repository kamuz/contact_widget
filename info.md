# Виджет формы обратной связи

Создадим основной файл плагина для нашего виджета, добавим хедер, поключим файлы стилей и скриптов, класс виджета и зарегистрируем новый виджет:

*contact-widget/contact-widget.php*

```php
<?php
/*
Plugin Name: AJAX Contact Widget
Description: Simple AJAX powered contact form widget
Version: 0.1.0
Author: Vladimir Kamuz
Author URI: https://wpdev.pp.ua
License: GPL2
*/

/**
 * Include JavaScript
 */
function add_scripts(){
    wp_enqueue_style('contact-style', plugins_url(). '/contact-widget/css/style.css', null, '1.0.0');
    wp_enqueue_script('contact-scripts', plugins_url(). '/contact-widget/js/script.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'add_scripts');

/**
 * Include Class
 */
include('class.contact-widget.php');

/**
 * Register Widget
 */
function register_contact_widget(){
    register_widget('Contact_Widget');
}
add_action('widget_init', 'register_contact_widget');
```

Проверим работу скрипта:

*wp-content/plugins/contact-widget/js/script.js*

```javascript
jQuery(document).ready(function($){
    console.log('Hi');
});
```

Опишем класс виджета:

*wp-content/plugins/contact-widget/class.contact-widget.php*

```php
<?php

class Contact_Widget extends WP_Widget{

    /**
     * Setup widget name and description
     */
    public function __construct() {
        $widget_ops = array( 
            'classname' => 'contact_widget',
            'description' => 'AJAX powered contact widget',
        );
        parent::__construct( 'contact_widget', 'AJAX Contact Widget', $widget_ops );
    }

    /**
     * Front-end display of widget
     */
    public function widget( $args, $instance ) {
        $title = apply_filters('widget_title', $instance['title']);
        $recipient = $instance['recipient'];
        $subject = $instance['subject'];

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo $this->getForm($recipient, $subject);

        echo $args['after_widget'];
    }

    /**
     * Backend form
     */
    public function form($instance){
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'AJAX Contact Widget', 'text_domain' );
        $recipient = ! empty( $instance['recipient'] ) ? $instance['recipient'] : esc_html__( 'Recipient', 'text_domain' );
        $subject = ! empty( $instance['subject'] ) ? $instance['subject'] : esc_html__( 'Subject', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title') ?>"><?php echo _e('Title:') ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('recipient') ?>"><?php echo _e('Recipient:') ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'recipient' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'recipient' ) ); ?>" type="text" value="<?php echo esc_attr( $recipient ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('subject') ?>"><?php echo _e('Subject:') ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'subject' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subject' ) ); ?>" type="text" value="<?php echo esc_attr( $subject ); ?>">
        </p>
        <?php
    }

    /**
     * Update Method
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['recipient'] = ( ! empty( $new_instance['recipient'] ) ) ? sanitize_text_field( $new_instance['recipient'] ) : '';
        $instance['subject'] = ( ! empty( $new_instance['subject'] ) ) ? sanitize_text_field( $new_instance['subject'] ) : '';

        return $instance;
    }

    /**
     * Display Contact Form
     */
    public function getForm($recipient, $subject){
        $output = '
        <div id="form-messages"></div>
        <form id="ajax-contact" method="post" action="' . plugins_url() . '/contact-widget/mailer.php">
            <p class="field">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </p>
            <p class="field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </p>
            <p class="field">
                <label for="name">Message:</label>
                <textarea id="message" name="message" required></textarea>
                <input name="recipient" type="hidden" value="' . $recipient . '">
                <input name="subject" type="hidden" value="' . $subject . '">
            </p>
            <p class="field">
                <input name="contact_submit" type="submit" value="Send">
            </p>
        </form>
        ';
        return $output;
    }

}
```

Напишем скрипт, который будет отправлять AJAX запрос на сервер:

*wp-content/plugins/contact-widget/js/script.js*

```js
jQuery(document).ready(function($){
    // Get Form
    var form = $('#ajax-contact');

    // Messages
    var formMessages = $('#form-messages');

    // Form Event Handler
    $(form).submit(function(event){
        // Stop browser from submitting form
        event.preventDefault();
        console.log("Contact form submited");

        // Serialize Data
        var formData = $(form).serialize();
        console.log(formData);

        // Submit with AJAX
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
        }).done(function(response){
            // Make sure message is success
            $(formMessages).removeClass('error');
            $(formMessages).addClass('success');

            // Set message
            $(formMessages).text(response);

            // Clear form fields
            $('#name').val('');
            $('#email').val('');
            $('#message').val('');
        }).fail(function(data){
            // Make sure message is error
            $(formMessages).removeClass('success');
            $(formMessages).addClass('error');

            // Set message text
            if(data.responseText !== ''){
                $(formMessages).text(data.responseText);
            }else{
                $(formMessages).text('An error occured');
            }
        });
    });
});
```

Напишем обработчик формы и сообщения формы в случае ошибок валидации или успешной отправки формы:

*wp-content/plugins/contact-widget/mailer.php*

```php
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
```

Добавим стили для наших сообщений:

*wp-content/plugins/contact-widget/css/style.css*

```css
.success{
    color: white;
    margin-bottom: 10px;
    background: green;
    padding: 10px;
    border-radius: 2px;
}
.error{
    color: white;
    margin-bottom: 10px;
    background: red;
    padding: 10px;
    border-radius: 2px;
}
```