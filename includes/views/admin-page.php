<?php
/**
 * Admin page view template.
 *
 * Variables available:
 *   $notice  — HTML notice string (empty if no save occurred)
 *   $data    — array of timeline items
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap horizontal-timeline-admin-wrap">
    <div class="horizontal-timeline-header">
        <h1>
            <span class="dashicons dashicons-clock" style="margin-right:8px;vertical-align:middle;"></span>
            Horizontal Timeline
        </h1>
    </div>

    <?php echo $notice; ?>

    <div class="horizontal-timeline-shortcode-hint">
        Use the shortcode <code>[horizontal_timeline]</code> to display your timeline on any page or post.
    </div>

    <form method="post" id="horizontal-timeline-admin-form">
        <?php wp_nonce_field('gst_timeline_save', 'gst_timeline_nonce'); ?>

        <div id="horizontal-timeline-cards-container">
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $i => $row): ?>
                <div class="horizontal-timeline-card" data-index="<?php echo $i; ?>">
                    <div class="horizontal-timeline-card-header" draggable="true">
                        <span class="horizontal-timeline-drag-handle dashicons dashicons-menu"></span>
                        <span class="horizontal-timeline-card-number"><?php echo $i + 1; ?></span>
                        <span class="horizontal-timeline-card-preview"><?php echo esc_html($row['year'] . ' — ' . $row['title']); ?></span>
                        <span class="horizontal-timeline-card-actions">
                            <button type="button" class="button button-small horizontal-timeline-btn-toggle" title="Collapse">
                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                            </button>
                            <button type="button" class="button button-small horizontal-timeline-btn-remove" title="Remove">&times;</button>
                        </span>
                    </div>
                    <div class="horizontal-timeline-card-body">
                        <label>Year / Date</label>
                        <input type="text" name="gst_timeline[<?php echo $i; ?>][year]"
                               value="<?php echo esc_attr($row['year']); ?>" placeholder="e.g. 2024" required>

                        <label>Title</label>
                        <input type="text" name="gst_timeline[<?php echo $i; ?>][title]"
                               value="<?php echo esc_attr($row['title']); ?>" placeholder="Event title" required>

                        <label>Description</label>
                        <?php
                        $desc_content = $row['desc'] ?? '';
                        $editor_id = 'gst_timeline_desc_' . $i;
                        wp_editor(
                            $desc_content,
                            $editor_id,
                            [
                                'textarea_name' => "gst_timeline[{$i}][desc]",
                                'media_buttons' => false,
                                'textarea_rows' => 6,
                                'teeny' => true,
                                'quicktags' => true,
                                'editor_class' => 'horizontal-timeline-richtext',
                            ]
                        );
                        ?>

                        <label>Image</label>
                        <div class="horizontal-timeline-image-field">
                            <?php
                                $image_id  = absint($row['image'] ?? 0);
                                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
                            ?>
                            <input type="hidden" name="gst_timeline[<?php echo $i; ?>][image]"
                                class="horizontal-timeline-image-id" value="<?php echo $image_id; ?>">
                            <div class="horizontal-timeline-image-preview">
                                <?php if ($image_url): ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button horizontal-timeline-upload-btn">
                                <span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span><?php echo $image_url ? 'Change' : 'Select'; ?> Image
                            </button>
                            <button type="button" class="button horizontal-timeline-remove-image-btn" style="<?php echo $image_url ? '' : 'display:none'; ?>">&times; Remove</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="horizontal-timeline-empty-state" id="horizontal-timeline-empty" style="<?php echo !empty($data) ? 'display:none' : ''; ?>">
            <span class="dashicons dashicons-clock"></span>
            <p>No timeline events yet. Click <strong>Add Event</strong> to get started.</p>
        </div>

        <div class="horizontal-timeline-toolbar">
            <button type="button" class="button" id="horizontal-timeline-add-row">
                <span class="dashicons dashicons-plus-alt2"
                      style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Add Event
            </button>
            <button type="button" class="button" id="horizontal-timeline-collapse-all">Collapse All</button>
            <button type="button" class="button" id="horizontal-timeline-expand-all">Expand All</button>
            <span class="horizontal-timeline-event-count" id="horizontal-timeline-count"><?php echo count($data); ?> event(s)</span>
            <span style="flex:1"></span>
            <input type="submit" class="button button-primary button-hero" value="Save Timeline">
        </div>
    </form>
</div>
