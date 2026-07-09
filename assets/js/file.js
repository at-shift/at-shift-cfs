(function($) {
    'use strict';

    var fileFrame = null;
    var fileFrameType = '';
    var activeButton = null;
    var hasAppliedSelection = false;

    function getSetting(key, fallback) {
        var settings = window.ATSHIFT_CFS_FILE || {};

        return settings[key] || fallback;
    }

    function normalizeFileType(fileType) {
        return -1 === $.inArray(fileType, ['image', 'audio', 'video']) ? 'file' : fileType;
    }

    function getPreviewUrl(attachment) {
        if (attachment.sizes) {
            if (attachment.sizes.medium) {
                return attachment.sizes.medium.url;
            }
            if (attachment.sizes.thumbnail) {
                return attachment.sizes.thumbnail.url;
            }
            if (attachment.sizes.full) {
                return attachment.sizes.full.url;
            }
        }

        return attachment.url;
    }

    function applySelection() {
        if (hasAppliedSelection || !fileFrame || !activeButton || !activeButton.length) {
            return;
        }

        var selection = fileFrame.state().get('selection');
        var model = selection ? selection.first() : null;

        if (!model) {
            return;
        }

        hasAppliedSelection = true;

        var attachment = model.toJSON();
        var $preview = activeButton.siblings('.file_url').empty();

        if ('image' === attachment.type) {
            $preview.append($('<img>', {
                src: getPreviewUrl(attachment),
                alt: '',
                class: 'cfs-file-preview-image'
            }));
        }
        else {
            $preview.append($('<a>', {
                href: attachment.url,
                target: '_blank',
                rel: 'noopener noreferrer',
                text: attachment.filename || attachment.url
            }));
        }

        activeButton.hide();
        activeButton.siblings('.media.button.remove').show();
        activeButton.siblings('.file_value').val(attachment.id);
    }

    function getFileFrame(fileType) {
        fileType = normalizeFileType(fileType);

        if (fileFrame && fileFrameType === fileType) {
            return fileFrame;
        }

        var frameOptions = {
            className: 'media-frame atshift-cfs-file-frame',
            frame: 'select',
            title: getSetting('title', 'Add File'),
            button: {
                text: getSetting('button', 'Add File')
            },
            multiple: false
        };

        if ('file' !== fileType) {
            frameOptions.library = {
                type: fileType
            };
        }

        fileFrame = wp.media(frameOptions);
        fileFrameType = fileType;

        fileFrame.on('select', applySelection);
        fileFrame.on('open', function() {
            var selection = fileFrame.state().get('selection');

            hasAppliedSelection = false;

            if (selection && selection.reset) {
                selection.reset();
            }
        });

        return fileFrame;
    }

    $(function() {
        $(document).on('click', '.cfs_file .media.button.add', function(e) {
            e.preventDefault();

            if ('undefined' === typeof wp || !wp.media) {
                return;
            }

            activeButton = $(this);
            getFileFrame(activeButton.attr('data-file-type')).open();
        });

        $(document).on('click', '.cfs_file .media.button.remove', function() {
            $(this).siblings('.file_url').html('');
            $(this).siblings('.file_value').val('');
            $(this).siblings('.media.button.add').show();
            $(this).hide();
        });
    });
})(jQuery);
