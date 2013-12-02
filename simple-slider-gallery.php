<?php
/*
Plugin Name: Simple Slider Gallery
Plugin URI: https://github.com/tonglil/simple-slider-gallery
Description: A WordPress plugin to create a gallery slide deck using the bxSlider (http://bxslider.com/)
Version: 1.0
Author: Tony Li
Author URI: http://tonyli.ca
*/

function parse_gallery_shortcode($atts) {
    global $post;

    // 'ids' is explicitly ordered, unless you specify otherwise.
    if (!empty($atts['ids'])) {
        if (empty($atts['orderby']))
            $atts['orderby'] = 'post__in';
        $atts['include'] = $atts['ids'];
    }

    extract(shortcode_atts(array(
        'orderby' => 'menu_order ASC, ID ASC',
        'include' => '',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'large',
        'link' => 'file'
    ), $atts));

    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'orderby' => $orderby
    );

    if (!empty($include)) {
        $args['include'] = $include;
    } else {
        $args['post_parent'] = $id;
        $args['numberposts'] = -1;
    }

    $images = get_posts($args);

    echo '<ul class="simple-slider-gallery">';
    foreach ($images as $image) {
        $caption = $image->post_excerpt;

        $description = $image->post_content;
        if($description == '') $description = $image->post_title;

        $image_alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);

        // render your gallery here
        echo '<li>';
        echo wp_get_attachment_image($image->ID, $size);
        echo '</li>';
    }
    echo '</ul>';
}
remove_shortcode('gallery');
add_shortcode('gallery', 'parse_gallery_shortcode');

function slider_requirements() {
    wp_register_style('slider-style', plugins_url('/slider.css', __FILE__));
    wp_register_script('slider-script', plugins_url('/slider.min.js', __FILE__), array('jquery'));
    wp_register_script('ready-script', plugins_url('/ready-slider.js', __FILE__));

    wp_enqueue_style('slider-style');
    wp_enqueue_script('slider-script');
    wp_enqueue_script('ready-script');
}
add_action('wp_enqueue_scripts', 'slider_requirements');
