<?php

/**
 * Plugin Name: Franco Virtual Tour
 * Plugin URI: https://github.com/AndersonFranco/franco-virtual-tour
 * Description: Street View Gallery.
 * Author: Anderson Franco
 * Author URI: http://www.francotecnologia.com/
 * Version: 1.0.0
 * License: GPLv2 or later
 */
class FrancoVirtualTour
{

    public function __construct()
    {
        register_activation_hook(__FILE__, array(get_called_class(), 'pluginActivation'));
        add_action('plugins_loaded', array(get_called_class(), 'actions'));
    }

    public function pluginActivation()
    {
        self::createPostType();
        flush_rewrite_rules();
    }

    public function actions()
    {
        add_action('init', array(get_called_class(), 'createPostType'));
        add_action('add_meta_boxes', array(get_called_class(), 'addMetaBoxes'));
        add_action('save_post', array(get_called_class(), 'savePost'));
        add_action('after_switch_theme', array(get_called_class(), 'pluginActivation'));
        add_action('wp_enqueue_scripts', array(get_called_class(), 'css'), 98);
        add_filter('template_include', array(get_called_class(), 'template'), 1);
        add_filter('pre_get_document_title', array(get_called_class(), 'pageTitle'), 100, 1);
    }

    public function createPostType()
    {
        register_post_type('franco_virtualtour',
            array(
                'labels' => array(
                    'name' => __('Virtual Tours'),
                    'singular_name' => __('Virtual Tour')
                ),
                'taxonomies' => array('category'),
                'public' => true,
                'supports' => array('title', 'editor', 'thumbnail'),
                'has_archive' => true,
                'rewrite' => array('slug' => 'virtualtour'),
            )
        );
    }

    public function addMetaBoxes()
    {
        add_meta_box('meta_box', __('Extras'), array(get_called_class(), 'metaBoxContent'), 'franco_virtualtour', 'normal', 'high');
    }

    public function metaBoxContent($post)
    {
        $streetviewUrl = esc_html(get_post_meta($post->ID, 'streetview_url', true));
        wp_nonce_field(plugin_basename(__FILE__), 'box_content_nonce');
        echo '<label for="streetview_url">Street View Embed URL</label>';
        echo '<input type="text" id="streetview_url" name="streetview_url" placeholder="Street View URL" value="' . $streetviewUrl . '" style="width:100%;" />';
    }

    public function savePost($postId)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!wp_verify_nonce($_POST['box_content_nonce'], plugin_basename(__FILE__))) return;

        if ('franco_virtualtour' == $_POST['post_type']) {
            update_post_meta($postId, 'streetview_url', $_POST['streetview_url']);
        }
    }

    public function css()
    {
        wp_enqueue_style('virtualtour', plugins_url('virtualtour.css', __FILE__), array(), '1.0', 'all');
    }

    public function template($templatePath)
    {
        if (get_post_type() == 'franco_virtualtour') {
            if ($themeFile = locate_template(array('streetview-virtualtour.php'))) {
                $templatePath = $themeFile;
            } else {
                $templatePath = plugin_dir_path(__FILE__) . '/streetview-virtualtour.php';
            }
        }
        return $templatePath;
    }

    public function pageTitle($title)
    {
        return is_single() ? $title : 'Google Street View - Virtual Tours';
    }

}

new FrancoVirtualTour();
