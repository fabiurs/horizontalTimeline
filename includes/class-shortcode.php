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

        $style_settings = get_option('gst_timeline_style', []);
        $year_style = '';
        $title_style = '';
        $text_style = '';
        if (is_array($style_settings)) {
            $year_style = sprintf('color:%s;font-size:%s;font-weight:%s;',
                esc_attr($style_settings['year_color'] ?? '#585058'),
                esc_attr($style_settings['year_size'] ?? '1.05rem'),
                esc_attr($style_settings['year_weight'] ?? '900')
            );
            $title_style = sprintf('color:%s;font-size:%s;font-weight:%s;',
                esc_attr($style_settings['title_color'] ?? '#000'),
                esc_attr($style_settings['title_size'] ?? '40px'),
                esc_attr($style_settings['title_weight'] ?? '900')
            );
            $text_style = sprintf('color:%s;font-size:%s;font-weight:%s;',
                esc_attr($style_settings['text_color'] ?? '#222'),
                esc_attr($style_settings['text_size'] ?? '1.1rem'),
                esc_attr($style_settings['text_weight'] ?? '400')
            );
        }
        ob_start(); ?>
        <div class="horizontal-timeline-wrapper">
            <button type="button" class="horizontal-timeline-toggle-all" style="margin: 10px 0 20px 20px;">Detalii</button>
            <div class="horizontal-timeline-container-lines">
                <span class="horizontal-timeline-bkg-lines"></span>
            </div>
            <div id="horizontal-timeline-container" class="horizontal-timeline-container">
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $item): ?>
                        <div class="horizontal-timeline-item">
                            <div class="horizontal-timeline-dot"></div>
                            <div class="horizontal-timeline-content">
                                <span class="horizontal-timeline-year" style="<?php echo $year_style; ?>"><?php echo esc_html($item['year']); ?></span>
                                <h3 class="horizontal-timeline-title" style="<?php echo $title_style; ?>"><?php echo esc_html($item['title']); ?></h3>
                                <?php
                                    $img_id = absint($item['image'] ?? 0);
                                    if ($img_id) {
                                        $img_url = wp_get_attachment_image_url($img_id, 'medium_large');
                                        if ($img_url) {
                                            echo '<div class="horizontal-timeline-image"><img src="' . esc_url($img_url) . '" alt="' . esc_attr($item['title']) . '" loading="lazy"></div>';
                                        }
                                    }
                                ?>
                                <p class="horizontal-timeline-text" style="<?php echo $text_style; ?>"><?php echo esc_html($item['desc']); ?></p>
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
