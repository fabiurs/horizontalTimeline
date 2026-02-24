<?php
/**
 * Shortcode rendering for [smooth_timeline].
 */

if (!defined('ABSPATH')) exit;

class GST_Shortcode {

    /**
     * Register the shortcode.
     */
    public static function init() {
        add_shortcode('smooth_timeline', [__CLASS__, 'render']);
    }

    /**
     * Render the timeline shortcode.
     */
    public static function render($atts) {
        wp_enqueue_style('gst-css');
        wp_enqueue_script('gst-js');

        $data = get_option('gst_timeline_data', []);

        ob_start(); ?>
        <div class="gst-wrapper">
            <div class="gst-progress-track">
                <div id="gst-bar"></div>
            </div>
            <div class="gst-container-lines">
                <span class="gst-bkg-lines"></span>
            </div>
            <div id="gst-container" class="gst-container">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $item): ?>
                        <div class="gst-item">
                            <div class="gst-dot"></div>
                            <div class="gst-content">
                                <?php
                                    $img_id = absint($item['image'] ?? 0);
                                    if ($img_id) {
                                        $img_url = wp_get_attachment_image_url($img_id, 'medium_large');
                                        if ($img_url) {
                                            echo '<div class="gst-image"><img src="' . esc_url($img_url) . '" alt="' . esc_attr($item['title']) . '" loading="lazy"></div>';
                                        }
                                    }
                                ?>
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
}
