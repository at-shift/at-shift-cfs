(function (blocks, element, i18n) {
    var config = window.CFSBlockEditor || {};
    var groups = config.groups || [];
    var createElement = element.createElement;
    var __ = i18n.__;

    groups.forEach(function (group) {
        blocks.registerBlockType(group.name, {
            apiVersion: 3,
            title: group.blockTitle,
            description: config.description || __('Displays a CFS field group.', 'atshift-fields-maintenance-for-custom-field-suite' ),
            category: config.category || 'atshift-cfs',
            icon: 'feedback',
            attributes: {
                groupId: {
                    type: 'number',
                    default: group.id
                }
            },
            supports: {
                html: false,
                reusable: false
            },
            edit: function () {
                return createElement(
                    'div',
                    { className: 'atshift-cfs-block-editor-placeholder' },
                    createElement('strong', {}, group.title),
                    createElement(
                        'span',
                        {},
                        (config.fieldGroup || __('Field Group', 'atshift-fields-maintenance-for-custom-field-suite' )) + ' / ' +
                            (config.fieldCount || __('Fields', 'atshift-fields-maintenance-for-custom-field-suite' )) + ': ' + group.fieldCount
                    ),
                    0 === group.fieldCount ? createElement('em', {}, config.noFields || __('No fields in this group.', 'atshift-fields-maintenance-for-custom-field-suite' )) : null
                );
            },
            save: function () {
                return null;
            }
        });
    });

    if (config.hidePanels && config.hidePanels.length && window.wp.domReady) {
        window.wp.domReady(function () {
            var hidePostTitle = config.hidePanels.indexOf('post-title') !== -1;
            var hideRevisions = config.hidePanels.indexOf('post-revisions') !== -1 || config.hidePanels.indexOf('revisions') !== -1;
            var panelNames = config.hidePanels.filter(function (panelName) {
                return 'post-title' !== panelName && 'post-revisions' !== panelName && 'revisions' !== panelName;
            });
            var editor;

            if (hidePostTitle) {
                document.body.classList.add('atshift-cfs-hide-post-title');
            }

            if (hideRevisions) {
                document.body.classList.add('atshift-cfs-hide-revisions');
            }

            if (!panelNames.length || !window.wp.data) {
                return;
            }

            editor = window.wp.data.dispatch('core/editor');

            if (!editor || 'function' !== typeof editor.removeEditorPanel) {
                editor = window.wp.data.dispatch('core/edit-post');
            }

            if (!editor || 'function' !== typeof editor.removeEditorPanel) {
                return;
            }

            panelNames.forEach(function (panelName) {
                editor.removeEditorPanel(panelName);
            });
        });
    }
})(window.wp.blocks, window.wp.element, window.wp.i18n);
