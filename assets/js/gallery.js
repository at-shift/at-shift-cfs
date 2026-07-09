(function($) {
    'use strict';

    var galleryFrame = null;
    var activeGallery = null;
    var hasAppliedSelection = false;

    function getSetting(key, fallback) {
        var settings = window.ATSHIFT_CFS_GALLERY || {};

        return settings[key] || fallback;
    }

    function updateGalleryValue($gallery) {
        var ids = [];

        $gallery.find('.cfs-gallery-item').each(function() {
            var id = parseInt($(this).attr('data-id'), 10);

            if (!isNaN(id) && 0 < id) {
                ids.push(id);
            }
        });

        $gallery.find('.gallery_value').val(ids.join(','));
        $gallery.find('.cfs-gallery-actions .clear').toggleClass('hidden', 0 === ids.length);
    }

    function getPreviewUrl(attachment) {
        if (attachment.sizes) {
            if (attachment.sizes.thumbnail) {
                return attachment.sizes.thumbnail.url;
            }
            if (attachment.sizes.medium) {
                return attachment.sizes.medium.url;
            }
            if (attachment.sizes.full) {
                return attachment.sizes.full.url;
            }
        }

        return attachment.url;
    }

    function addGalleryImage($gallery, attachment) {
        var id = parseInt(attachment.id, 10);

        if (isNaN(id) || 1 > id || $gallery.find('.cfs-gallery-item[data-id="' + id + '"]').length) {
            return;
        }

        $('<div></div>')
            .addClass('cfs-gallery-item')
            .attr('data-id', id)
            .append($('<span></span>').addClass('cfs-gallery-drag').attr('aria-hidden', 'true'))
            .append($('<img>', {
                src: getPreviewUrl(attachment),
                alt: '',
                class: 'cfs-gallery-preview-image'
            }))
            .append($('<button></button>', {
                type: 'button',
                class: 'cfs-gallery-remove',
                'aria-label': getSetting('removeImage', 'Remove image'),
                text: '\u00d7'
            }))
            .appendTo($gallery.find('.cfs-gallery-items'));
    }

    $.fn.init_cfs_gallery = function() {
        this.each(function() {
            var $gallery = $(this);

            if ($gallery.hasClass('ready')) {
                return;
            }

            $gallery.addClass('ready');

            if ($.fn.sortable) {
                $gallery.find('.cfs-gallery-items').sortable({
                    items: '.cfs-gallery-item',
                    update: function() {
                        updateGalleryValue($gallery);
                    }
                });
            }

            updateGalleryValue($gallery);
        });
    };

    function initGalleries() {
        $('.cfs_gallery .cfs-gallery-control:not(.ready)').init_cfs_gallery();
    }

    $(function() {
        initGalleries();

        $(document).on('cfs/ready', '.cfs_add_field', function() {
            initGalleries();
        });

        function applySelection() {
            if (hasAppliedSelection || !activeGallery || !activeGallery.length || !galleryFrame) {
                return;
            }

            hasAppliedSelection = true;
            galleryFrame.state().get('selection').each(function(model) {
                addGalleryImage(activeGallery, model.toJSON());
            });
            updateGalleryValue(activeGallery);
        }

        function getGalleryFrame() {
            if ('undefined' === typeof wp || !wp.media) {
                return null;
            }

            if (galleryFrame) {
                return galleryFrame;
            }

            galleryFrame = wp.media({
                className: 'media-frame atshift-cfs-gallery-frame',
                frame: 'select',
                title: getSetting('selectImages', 'Select Gallery Images'),
                button: {
                    text: getSetting('addImages', 'Add Images')
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            galleryFrame.on('insert', applySelection);
            galleryFrame.on('select', applySelection);
            galleryFrame.on('open', function() {
                var selection = galleryFrame.state().get('selection');

                hasAppliedSelection = false;

                if (selection && selection.reset) {
                    selection.reset();
                }
            });

            return galleryFrame;
        }

        $(document).on('click', '.cfs_gallery .media.button.add', function(e) {
            e.preventDefault();

            var frame = getGalleryFrame();

            if (!frame) {
                return;
            }

            activeGallery = $(this).closest('.cfs-gallery-control');
            frame.open();
        });

        $(document).on('click', '.cfs_gallery .cfs-gallery-remove', function() {
            var $gallery = $(this).closest('.cfs-gallery-control');
            $(this).closest('.cfs-gallery-item').remove();
            updateGalleryValue($gallery);
        });

        $(document).on('click', '.cfs_gallery .cfs-gallery-actions .clear', function() {
            var $gallery = $(this).closest('.cfs-gallery-control');
            $gallery.find('.cfs-gallery-items').empty();
            updateGalleryValue($gallery);
        });
    });
})(jQuery);
