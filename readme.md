# Виджет формы обратной связи

Создадим основной файл плагина для нашего виджета, добавим хедер, поключим JavaScript скрипт, класс виджета и зарегистрируем новый виджет:

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