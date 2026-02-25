<?php
/**
 * Admin page: menu registration, form handling, asset enqueuing.
 */

if (!defined('ABSPATH')) exit;

class GST_Admin {
    /**
     * Add settings for year, title, and text styles.
     */
    private static function get_style_settings() {
        $defaults = [
            'year_color' => '#585058',
            'year_size' => '1.05rem',
            'year_weight' => '900',
            'title_color' => '#000',
            'title_size' => '40px',
            'title_weight' => '900',
            'text_color' => '#222',
            'text_size' => '1.1rem',
            'text_weight' => '400',
        ];
        $saved = get_option('gst_timeline_style', []);
        return array_merge($defaults, is_array($saved) ? $saved : []);
    }

    private static function save_style_settings() {
        $fields = [
            'year_color', 'year_size', 'year_weight',
            'title_color', 'title_size', 'title_weight',
            'text_color', 'text_size', 'text_weight',
        ];
        $settings = [];
        foreach ($fields as $f) {
            $settings[$f] = sanitize_text_field($_POST[$f] ?? '');
        }
        update_option('gst_timeline_style', $settings);
    }

    /**
     * Hook into WordPress admin.
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    /**
     * Register the admin menu page.
     */
    public static function add_menu() {
        add_menu_page(
            'Horizontal Timeline',
            'Horizontal Timeline',
            'manage_options',
            'gst_timeline_admin',
            [__CLASS__, 'render_page'],
            'dashicons-calendar',
            20
        );
    }

    /**
     * Enqueue admin-only CSS & JS on our settings page.
     */
    public static function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_gst_timeline_admin') return;

        // WordPress media uploader
        wp_enqueue_media();

        wp_enqueue_style(
            'horizontal-timeline-admin-css',
            plugins_url('assets/admin-style.css', dirname(__FILE__)),
            [],
            '1.4'
        );
        wp_enqueue_script(
            'horizontal-timeline-admin-js',
            plugins_url('assets/admin-script.js', dirname(__FILE__)),
            ['jquery'],
            '1.4',
            true
        );
    }

    /**
     * Process form submission and render the admin view.
     */
    public static function render_page() {
        if (!current_user_can('manage_options')) return;

        $notice = '';

        // Handle timeline save
        if (isset($_POST['gst_timeline_nonce']) && wp_verify_nonce($_POST['gst_timeline_nonce'], 'gst_timeline_save')) {
            $notice = self::handle_save();
        }
        // Handle style settings save
        if (isset($_POST['gst_timeline_style_nonce']) && wp_verify_nonce($_POST['gst_timeline_style_nonce'], 'gst_timeline_style_save')) {
            self::save_style_settings();
            $notice .= '<div class="notice notice-success is-dismissible"><p><strong>Style settings saved.</strong></p></div>';
        }

        $data = get_option('gst_timeline_data', []);
        $style_settings = self::get_style_settings();

        // Load the view template
        include plugin_dir_path(__FILE__) . '../includes/views/admin-page.php';
    }

    /**
     * Sanitize and persist timeline data.
     *
     * @return string HTML notice markup.
     */
    private static function handle_save(): string {
        $raw   = isset($_POST['gst_timeline']) ? $_POST['gst_timeline'] : [];
        $clean = [];

        if (is_array($raw)) {
            foreach ($raw as $row) {
                if (!empty($row['year']) && !empty($row['title'])) {
                    $clean[] = [
                        'year'  => sanitize_text_field($row['year']),
                        'title' => sanitize_text_field($row['title']),
                        'desc'  => sanitize_textarea_field($row['desc'] ?? ''),
                        'image' => absint($row['image'] ?? 0),
                    ];
                }
            }
        }

        update_option('gst_timeline_data', $clean);

        return '<div class="notice notice-success is-dismissible"><p><strong>Timeline saved.</strong> '
            . count($clean) . ' event(s) stored.</p></div>';
    }
}
