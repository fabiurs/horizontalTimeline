/**
 * Horizontal Timeline — Admin Script
 *
 * Card CRUD, drag-and-drop reordering, collapse/expand, live preview.
 */
(function ($) {
    'use strict';

    const $container = $('#gst-cards-container');
    const $empty     = $('#gst-empty');
    const $count     = $('#gst-count');

    /* ── Helpers ─────────────────────────────────────────── */

    function reindex() {
        $container.find('.gst-card').each(function (i) {
            const $card = $(this);
            $card.attr('data-index', i);
            $card.find('.gst-card-number').text(i + 1);
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
        $card.find('.gst-card-preview').text(year + ' — ' + title);
    }

    function buildCardHtml(index) {
        return `
            <div class="gst-card" data-index="${index}">
                <div class="gst-card-header" draggable="true">
                    <span class="gst-drag-handle dashicons dashicons-menu"></span>
                    <span class="gst-card-number">${index + 1}</span>
                    <span class="gst-card-preview">— Untitled</span>
                    <span class="gst-card-actions">
                        <button type="button" class="button button-small gst-btn-toggle" title="Collapse">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <button type="button" class="button button-small gst-btn-remove" title="Remove">&times;</button>
                    </span>
                </div>
                <div class="gst-card-body">
                    <label>Year / Date</label>
                    <input type="text" name="gst_timeline[${index}][year]" placeholder="e.g. 2024" required>
                    <label>Title</label>
                    <input type="text" name="gst_timeline[${index}][title]" placeholder="Event title" required>
                    <label>Description</label>
                    <textarea name="gst_timeline[${index}][desc]" placeholder="Brief description of this event…"></textarea>
                    <label>Image</label>
                    <div class="gst-image-field">
                        <input type="hidden" name="gst_timeline[${index}][image]" class="gst-image-id" value="0">
                        <div class="gst-image-preview"></div>
                        <button type="button" class="button gst-upload-btn">
                            <span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Select Image
                        </button>
                        <button type="button" class="button gst-remove-image-btn" style="display:none">&times; Remove</button>
                    </div>
                </div>
            </div>`;
    }

    /* ── Add Event ───────────────────────────────────────── */

    $('#gst-add-row').on('click', function () {
        const i     = $container.children().length;
        const $card = $(buildCardHtml(i));
        $container.append($card);
        $card.find('input:first').focus();
        reindex();
    });

    /* ── Remove Event ────────────────────────────────────── */

    $container.on('click', '.gst-btn-remove', function () {
        $(this).closest('.gst-card').slideUp(200, function () {
            $(this).remove();
            reindex();
        });
    });

    /* ── Toggle Collapse ─────────────────────────────────── */

    $container.on('click', '.gst-btn-toggle', function () {
        const $btn  = $(this);
        const $body = $btn.closest('.gst-card').find('.gst-card-body');
        $body.toggleClass('is-collapsed');
        $btn.toggleClass('is-collapsed');
    });

    $('#gst-collapse-all').on('click', function () {
        $container.find('.gst-card-body').addClass('is-collapsed');
        $container.find('.gst-btn-toggle').addClass('is-collapsed');
    });

    $('#gst-expand-all').on('click', function () {
        $container.find('.gst-card-body').removeClass('is-collapsed');
        $container.find('.gst-btn-toggle').removeClass('is-collapsed');
    });

    /* ── Live Preview Update ─────────────────────────────── */

    $container.on('input', 'input[name*="[year]"], input[name*="[title]"]', function () {
        updatePreview($(this).closest('.gst-card'));
    });

    /* ── Drag & Drop Reorder ─────────────────────────────── */

    let dragCard = null;

    $container.on('dragstart', '.gst-card-header', function (e) {
        dragCard = $(this).closest('.gst-card')[0];
        dragCard.classList.add('is-dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/plain', '');
    });

    $container.on('dragend', '.gst-card-header', function () {
        if (dragCard) dragCard.classList.remove('is-dragging');
        dragCard = null;
        $container.find('.gst-drop-indicator').remove();
    });

    $container.on('dragover', '.gst-card', function (e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        if (!dragCard || this === dragCard) return;

        $container.find('.gst-drop-indicator').remove();
        const rect      = this.getBoundingClientRect();
        const midY      = rect.top + rect.height / 2;
        const $indicator = $('<div class="gst-drop-indicator"></div>');

        if (e.originalEvent.clientY < midY) {
            $(this).before($indicator);
        } else {
            $(this).after($indicator);
        }
    });

    $container.on('drop', '.gst-card', function (e) {
        e.preventDefault();
        if (!dragCard || this === dragCard) return;

        const rect = this.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        if (e.originalEvent.clientY < midY) {
            $(this).before(dragCard);
        } else {
            $(this).after(dragCard);
        }

        $container.find('.gst-drop-indicator').remove();
        dragCard.classList.remove('is-dragging');
        dragCard = null;
        reindex();
    });

    /* ── Media Uploader (Image picker) ─────────────────────── */

    $container.on('click', '.gst-upload-btn', function (e) {
        e.preventDefault();
        const $field   = $(this).closest('.gst-image-field');
        const $input   = $field.find('.gst-image-id');
        const $preview = $field.find('.gst-image-preview');
        const $removeBtn = $field.find('.gst-remove-image-btn');
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

    $container.on('click', '.gst-remove-image-btn', function () {
        const $field   = $(this).closest('.gst-image-field');
        $field.find('.gst-image-id').val('0');
        $field.find('.gst-image-preview').empty();
        $field.find('.gst-upload-btn').html('<span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:4px;font-size:16px;width:16px;height:16px;"></span>Select Image');
        $(this).hide();
    });

    /* ── Keyboard: Ctrl+S to save ────────────────────────── */

    $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            $('#gst-admin-form').submit();
        }
    });

})(jQuery);
