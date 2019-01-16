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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'AJAX Contact Widget', 'kmzcontact' );
        $recipient = ! empty( $instance['recipient'] ) ? $instance['recipient'] : esc_html__( 'Recipient', 'kmzcontact' );
        $subject = ! empty( $instance['subject'] ) ? $instance['subject'] : esc_html__( 'Subject', 'kmzcontact' );
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