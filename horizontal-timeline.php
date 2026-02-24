<?php
/**
 * Plugin Name: Horizontal Timeline
 * Description: A premium horizontal scroll timeline with inertia and drag support.
 * Version: 1.3
 * Author: Fabian Ursache
 */

if (!defined('ABSPATH')) exit;

/* ── Autoload plugin classes ────────────────────────────── */
require_once plugin_dir_path(__FILE__) . 'includes/class-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';

class HorizontalSmoothTimeline {

    public function __construct() {
        // Register front-end assets
        add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);

        // Shortcode
        GST_Shortcode::init();

        // Admin
        if (is_admin()) {
            GST_Admin::init();
        }
    }

    /**
     * Register (not enqueue) front-end CSS & JS.
     * They are enqueued on-demand inside the shortcode.
     */
    public function register_frontend_assets() {
        wp_register_style('gst-css',  plugins_url('assets/style.css',  __FILE__), [], '1.3');
        wp_register_script('gst-js',  plugins_url('assets/script.js',  __FILE__), [], '1.3', true);
    }
}

new HorizontalSmoothTimeline();