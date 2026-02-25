<?php
/**
 * Shortcode rendering for [horizontal_timeline].
 */

if (!defined('ABSPATH')) exit;

class GST_Shortcode {

    /**
     * Register the shortcode.
     */
    public static function init() {
        add_shortcode('horizontal_timeline', [__CLASS__, 'render']);
    }

    /**
     * Render the timeline shortcode.
     */
    public static function render($atts) {
        wp_enqueue_script('horizontal-timeline-js');

        $data = get_option('gst_timeline_data', []);

        ob_start(); ?>
        <div class="horizontal-timeline-wrapper">
            <div class="horizontal-timeline-container-lines">
                <span class="horizontal-timeline-bkg-lines"></span>
            </div>
            <div id="horizontal-timeline-container" class="horizontal-timeline-container">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $item): ?>
                        <div class="horizontal-timeline-item">
                            <div class="horizontal-timeline-dot"></div>
                            <div class="horizontal-timeline-content">
                                <?php
                                    $img_id = absint($item['image'] ?? 0);
                                    if ($img_id) {
                                        $img_url = wp_get_attachment_image_url($img_id, 'medium_large');
                                        if ($img_url) {
                                            echo '<div class="horizontal-timeline-image"><img src="' . esc_url($img_url) . '" alt="' . esc_attr($item['title']) . '" loading="lazy"></div>';
                                        }
                                    }
                                ?>
                                <span class="horizontal-timeline-year"><?php echo esc_html($item['year']); ?></span>
                                <h3 class="horizontal-timeline-title"><?php echo esc_html($item['title']); ?></h3>
                                <p class="horizontal-timeline-text"><?php echo esc_html($item['desc']); ?></p>
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
