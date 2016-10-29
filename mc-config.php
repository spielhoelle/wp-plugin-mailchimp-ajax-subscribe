<?php

function add_mc_scripts() {
    wp_register_script('newsletter-ajax', plugin_dir_url(__FILE__) .'/newsletter-ajax.js',array(),NULL,true);
    wp_enqueue_script('newsletter-ajax');

    $tommy_plugin_path = array( 'template_url' => plugin_dir_url(__FILE__));
    wp_localize_script( 'newsletter-ajax', 'tommy_plugin_path', $tommy_plugin_path );
}

add_action('wp_enqueue_scripts', 'add_mc_scripts');
