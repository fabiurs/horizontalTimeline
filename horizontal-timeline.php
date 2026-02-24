<?php
/**
 * Plugin Name: Horizontal Timeline
 * Description: A premium horizontal scroll timeline with inertia and drag support.
 * Version: 1.2
 * Author: Fabian Ursache
 */

if (!defined('ABSPATH')) exit;

class HorizontalSmoothTimeline {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_shortcode('smooth_timeline', [$this, 'render_shortcode']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function register_assets() {
        wp_register_style('gst-css', plugins_url('assets/style.css', __FILE__));
        wp_register_script('gst-js', plugins_url('assets/script.js', __FILE__), [], '1.0', true);
    }

    // Removed custom post type registration

    public function render_shortcode($atts) {
        wp_enqueue_style('gst-css');
        wp_enqueue_script('gst-js');

        $data = get_option('gst_timeline_data', []);

        ob_start(); ?>
        <div class="gst-wrapper">
            <div class="gst-progress-track"><div id="gst-bar"></div></div>
            <div id="gst-container" class="gst-container">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $item): ?>
                        <div class="gst-item">
                            <div class="gst-dot"></div>
                            <div class="gst-content">
                                <span class="gst-year"><?php echo esc_html($item['year']); ?></span>
                                <h3 class="gst-title"><?php echo esc_html($item['title']); ?></h3>
                                <p class="gst-text"><?php echo esc_html($item['desc']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No timeline events found.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function add_admin_menu() {
        add_menu_page(
            'Horizontal Timeline',
            'Horizontal Timeline',
            'manage_options',
            'gst_timeline_admin',
            [$this, 'admin_page_html'],
            'dashicons-clock',
            20
        );
    }

    public function admin_page_html() {
        if (!current_user_can('manage_options')) return;

        // Handle form submission
        if (isset($_POST['gst_timeline_nonce']) && wp_verify_nonce($_POST['gst_timeline_nonce'], 'gst_timeline_save')) {
            $data = isset($_POST['gst_timeline']) ? $_POST['gst_timeline'] : [];
            // Sanitize and save
            $clean = [];
            if (is_array($data)) {
                foreach ($data as $row) {
                    if (!empty($row['year']) && !empty($row['title'])) {
                        $clean[] = [
                            'year' => sanitize_text_field($row['year']),
                            'title' => sanitize_text_field($row['title']),
                            'desc' => sanitize_textarea_field($row['desc'])
                        ];
                    }
                }
            }
            update_option('gst_timeline_data', $clean);
            ?><div class="updated"><p>Timeline updated.</p></div><?php
        }

        $data = get_option('gst_timeline_data', []);
        ?>
        <div class="wrap">
            <h1>Horizontal Timeline Data</h1>
            <form method="post">
                <?php wp_nonce_field('gst_timeline_save', 'gst_timeline_nonce'); ?>
                <table class="form-table" id="gst-timeline-table">
                    <thead>
                        <tr><th>Year</th><th>Title</th><th>Description</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data)): foreach ($data as $i => $row): ?>
                        <tr>
                            <td><input type="text" name="gst_timeline[<?php echo $i; ?>][year]" value="<?php echo esc_attr($row['year']); ?>" required></td>
                            <td><input type="text" name="gst_timeline[<?php echo $i; ?>][title]" value="<?php echo esc_attr($row['title']); ?>" required></td>
                            <td><textarea name="gst_timeline[<?php echo $i; ?>][desc]" rows="2"><?php echo esc_textarea($row['desc']); ?></textarea></td>
                            <td><button type="button" class="button gst-remove-row">Remove</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                <p><button type="button" class="button" id="gst-add-row">Add Row</button></p>
                <p><input type="submit" class="button button-primary" value="Save Timeline"></p>
            </form>
        </div>
        <script>
        (function($){
            $('#gst-add-row').on('click', function(){
                var i = $('#gst-timeline-table tbody tr').length;
                $('#gst-timeline-table tbody').append('<tr>' +
                    '<td><input type="text" name="gst_timeline['+i+'][year]" required></td>' +
                    '<td><input type="text" name="gst_timeline['+i+'][title]" required></td>' +
                    '<td><textarea name="gst_timeline['+i+'][desc]" rows="2"></textarea></td>' +
                    '<td><button type="button" class="button gst-remove-row">Remove</button></td>' +
                '</tr>');
            });
            $(document).on('click', '.gst-remove-row', function(){
                $(this).closest('tr').remove();
            });
        })(jQuery);
        </script>
        <?php
    }
}

new HorizontalSmoothTimeline();