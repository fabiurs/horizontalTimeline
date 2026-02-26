/**
 * Horizontal Timeline — Admin Script
 *
 * Card CRUD, drag-and-drop reordering, collapse/expand, live preview.
 */
(function ($) {
    'use strict';

    const $container = $('#horizontal-timeline-cards-container');
    const $empty     = $('#horizontal-timeline-empty');
    const $count     = $('#horizontal-timeline-count');

    /* ── Helpers ─────────────────────────────────────────── */

    function reindex() {
        $container.find('.horizontal-timeline-card').each(function (i) {
            const $card = $(this);
            $card.attr('data-index', i);
            $card.find('.horizontal-timeline-card-number').text(i + 1);
            $card.find('input, textarea').each(function () {
                const name = $(this).attr('name');
                if (name) $(this).attr('name', name.replace(/\[\d+\]/, '[' + i + ']'));
            });
        });
        const total = $container.children().length;
        $count.text(total + ' event(s)');
        $empty.toggle(total === 0);
    }

    function updatePreview($card) {
        const year  = $card.find('input[name*="[year]"]').val()  || '—';
        const title = $card.find('input[name*="[title]"]').val() || 'Untitled';
        $card.find('.horizontal-timeline-card-preview').text(year + ' — ' + title);
    }

    function buildCardHtml(index) {
        return `
            <div class="horizontal-timeline-card" data-index="${index}">
                <div class="horizontal-timeline-card-header" draggable="true">
                    <span class="horizontal-timeline-drag-handle dashicons dashicons-menu"></span>
                    <span class="horizontal-timeline-card-number">${index + 1}</span>
                    <span class="horizontal-timeline-card-preview">— Untitled</span>
                    <span class="horizontal-timeline-card-actions">
                        <button type="button" class="button button-small horizontal-timeline-btn-toggle" title="Collapse">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <button type="button" class="button button-small horizontal-timeline-btn-remove" title="Remove">&times;</button>
                    </span>
                </div>
                <div class="horizontal-timeline-card-body">
                    <label>Year / Date</label>
                    <input type="text" name="gst_timeline[${index}][year]" placeholder="e.g. 2024" required>
                    <label>Title</label>
                    <input type="text" name="gst_timeline[${index}][title]" placeholder="Event title" required>
                    <label>Description</label>
                    <textarea name="gst_timeline[${index}][desc]" placeholder="Brief description of this event…"></textarea>
                    <label>Image</label>
                    <div class="horizontal-timeline-image-field">
                        <input type="hidden" name="gst_timeline[${index}][image]" class="horizontal-timeline-image-id" value="0">
                        <div class="horizontal-timeline-image-preview"></div>
                        <button type="button" class="button horizontal-timeline-upload-btn">
                            <span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Select Image
                        </button>
                        <button type="button" class="button horizontal-timeline-remove-image-btn" style="display:none">&times; Remove</button>
                    </div>
                </div>
            </div>`;
    }

    /* ── Add Event ───────────────────────────────────────── */

    $('#horizontal-timeline-add-row').on('click', function () {
        const i     = $container.children().length;
        const $card = $(buildCardHtml(i));
        $container.append($card);
        $card.find('input:first').focus();
        reindex();
    });

    /* ── Remove Event ────────────────────────────────────── */

    $container.on('click', '.horizontal-timeline-btn-remove', function () {
        $(this).closest('.horizontal-timeline-card').slideUp(200, function () {
            $(this).remove();
            reindex();
        });
    });

    /* ── Toggle Collapse ─────────────────────────────────── */

    $container.on('click', '.horizontal-timeline-btn-toggle', function () {
        const $btn  = $(this);
        const $body = $btn.closest('.horizontal-timeline-card').find('.horizontal-timeline-card-body');
        $body.toggleClass('is-collapsed');
        $btn.toggleClass('is-collapsed');
    });

    $('#horizontal-timeline-collapse-all').on('click', function () {
        $container.find('.horizontal-timeline-card-body').addClass('is-collapsed');
        $container.find('.horizontal-timeline-btn-toggle').addClass('is-collapsed');
    });

    $('#horizontal-timeline-expand-all').on('click', function () {
        $container.find('.horizontal-timeline-card-body').removeClass('is-collapsed');
        $container.find('.horizontal-timeline-btn-toggle').removeClass('is-collapsed');
    });

    /* ── Live Preview Update ─────────────────────────────── */

    $container.on('input', 'input[name*="[year]"], input[name*="[title]"]', function () {
        updatePreview($(this).closest('.horizontal-timeline-card'));
    });

    /* ── Drag & Drop Reorder ─────────────────────────────── */

    let dragCard = null;

    $container.on('dragstart', '.horizontal-timeline-card-header', function (e) {
        dragCard = $(this).closest('.horizontal-timeline-card')[0];
        dragCard.classList.add('is-dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/plain', '');
    });

    $container.on('dragend', '.horizontal-timeline-card-header', function () {
        if (dragCard) dragCard.classList.remove('is-dragging');
        dragCard = null;
        $container.find('.horizontal-timeline-drop-indicator').remove();
    });

    $container.on('dragover', '.horizontal-timeline-card', function (e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        if (!dragCard || this === dragCard) return;

        $container.find('.horizontal-timeline-drop-indicator').remove();
        const rect      = this.getBoundingClientRect();
        const midY      = rect.top + rect.height / 2;
        const $indicator = $('<div class="horizontal-timeline-drop-indicator"></div>');

        if (e.originalEvent.clientY < midY) {
            $(this).before($indicator);
        } else {
            $(this).after($indicator);
        }
    });

    $container.on('drop', '.horizontal-timeline-card', function (e) {
        e.preventDefault();
        if (!dragCard || this === dragCard) return;

        const rect = this.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        if (e.originalEvent.clientY < midY) {
            $(this).before(dragCard);
        } else {
            $(this).after(dragCard);
        }

        $container.find('.horizontal-timeline-drop-indicator').remove();
        dragCard.classList.remove('is-dragging');
        dragCard = null;
        reindex();
    });

    /* ── Media Uploader (Image picker) ─────────────────────── */

    $container.on('click', '.horizontal-timeline-upload-btn', function (e) {
        e.preventDefault();
        const $field   = $(this).closest('.horizontal-timeline-image-field');
        const $input   = $field.find('.horizontal-timeline-image-id');
        const $preview = $field.find('.horizontal-timeline-image-preview');
        const $removeBtn = $field.find('.horizontal-timeline-remove-image-btn');
        const $uploadBtn = $(this);

        const frame = wp.media({
            title: 'Select Timeline Image',
            button: { text: 'Use this image' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            const url = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;
            $input.val(attachment.id);
            $preview.html('<img src="' + url + '" alt="">');
            $uploadBtn.find('span').next().addBack().last();
            $uploadBtn.html('<span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Change Image');
            $removeBtn.show();
        });

        frame.open();
    });

    $container.on('click', '.horizontal-timeline-remove-image-btn', function () {
        const $field   = $(this).closest('.horizontal-timeline-image-field');
        $field.find('.horizontal-timeline-image-id').val('0');
        $field.find('.horizontal-timeline-image-preview').empty();
        $field.find('.horizontal-timeline-upload-btn').html('<span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Select Image');
        $(this).hide();
    });

    /* ── Sound Uploader ─────────────────────────────────── */

    $('.horizontal-timeline-upload-sound-btn').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const $soundField = $('#horizontal-timeline-sound-id');
        const $preview = $('.horizontal-timeline-sound-preview');
        const $removeBtn = $('.horizontal-timeline-remove-sound-btn');

        const mediaUploader = wp.media({
            title: 'Select Sound',
            button: {
                text: 'Use this sound'
            },
            library: {
                type: 'audio'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $soundField.val(attachment.id);
            $preview.html('<p>Selected Sound: <strong>' + attachment.filename + '</strong></p>');
            $button.html('<span class="dashicons dashicons-format-audio" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Change Sound');
            $removeBtn.show();
        });

        mediaUploader.open();
    });

    $('.horizontal-timeline-remove-sound-btn').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const $soundField = $('#horizontal-timeline-sound-id');
        const $preview = $('.horizontal-timeline-sound-preview');
        const $uploadBtn = $('.horizontal-timeline-upload-sound-btn');

        $soundField.val('0');
        $preview.html('<p>No sound selected.</p>');
        $uploadBtn.html('<span class="dashicons dashicons-format-audio" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Select Sound');
        $button.hide();
    });

    /* ── Keyboard: Ctrl+S to save ────────────────────────── */

    $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            $('#horizontal-timeline-admin-form').submit();
        }
    });

})(jQuery);
