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

<div class="wrap gst-admin-wrap">
    <div class="gst-header">
        <h1>
            <span class="dashicons dashicons-clock" style="margin-right:8px;vertical-align:middle;"></span>
            Horizontal Timeline
        </h1>
    </div>

    <?php echo $notice; ?>

    <div class="gst-shortcode-hint">
        Use the shortcode <code>[horizontal_timeline]</code> to display your timeline on any page or post.
    </div>

    <form method="post" id="gst-admin-form">
        <?php wp_nonce_field('gst_timeline_save', 'gst_timeline_nonce'); ?>

        <div id="gst-cards-container">
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $i => $row): ?>
                <div class="gst-card" data-index="<?php echo $i; ?>">
                    <div class="gst-card-header" draggable="true">
                        <span class="gst-drag-handle dashicons dashicons-menu"></span>
                        <span class="gst-card-number"><?php echo $i + 1; ?></span>
                        <span class="gst-card-preview"><?php echo esc_html($row['year'] . ' — ' . $row['title']); ?></span>
                        <span class="gst-card-actions">
                            <button type="button" class="button button-small gst-btn-toggle" title="Collapse">
                                <span class="dashicons dashicons-arrow-down-alt2"></span>
                            </button>
                            <button type="button" class="button button-small gst-btn-remove" title="Remove">&times;</button>
                        </span>
                    </div>
                    <div class="gst-card-body">
                        <label>Year / Date</label>
                        <input type="text" name="gst_timeline[<?php echo $i; ?>][year]"
                               value="<?php echo esc_attr($row['year']); ?>" placeholder="e.g. 2024" required>

                        <label>Title</label>
                        <input type="text" name="gst_timeline[<?php echo $i; ?>][title]"
                               value="<?php echo esc_attr($row['title']); ?>" placeholder="Event title" required>

                        <label>Description</label>
                        <textarea name="gst_timeline[<?php echo $i; ?>][desc]"
                                  placeholder="Brief description of this event…"><?php echo esc_textarea($row['desc'] ?? ''); ?></textarea>

                        <label>Image</label>
                        <div class="gst-image-field">
                            <?php
                                $image_id  = absint($row['image'] ?? 0);
                                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
                            ?>
                            <input type="hidden" name="gst_timeline[<?php echo $i; ?>][image]"
                                   class="gst-image-id" value="<?php echo $image_id; ?>">
                            <div class="gst-image-preview">
                                <?php if ($image_url): ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <button type="button" class="button gst-upload-btn">
                                <span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span><?php echo $image_url ? 'Change' : 'Select'; ?> Image
                            </button>
                            <button type="button" class="button gst-remove-image-btn" style="<?php echo $image_url ? '' : 'display:none'; ?>">&times; Remove</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="gst-empty-state" id="gst-empty" style="<?php echo !empty($data) ? 'display:none' : ''; ?>">
            <span class="dashicons dashicons-clock"></span>
            <p>No timeline events yet. Click <strong>Add Event</strong> to get started.</p>
        </div>

        <div class="gst-toolbar">
            <button type="button" class="button" id="gst-add-row">
                <span class="dashicons dashicons-plus-alt2"
                      style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Add Event
            </button>
            <button type="button" class="button" id="gst-collapse-all">Collapse All</button>
            <button type="button" class="button" id="gst-expand-all">Expand All</button>
            <span class="gst-event-count" id="gst-count"><?php echo count($data); ?> event(s)</span>
            <span style="flex:1"></span>
            <input type="submit" class="button button-primary button-hero" value="Save Timeline">
        </div>
    </form>
</div>
