// Run with node --test tests/verify-wysiwyg-lifecycle.cjs (PHP must be on PATH).
// These lifecycle unit tests complement, not replace, real iframe/browser tests.
const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const path = require('node:path');
const { test } = require('node:test');
const vm = require('node:vm');

const file = path.resolve(__dirname, '../includes/fields/wysiwyg.php');
const script = execFileSync(process.env.PHP_BINARY || 'php', ['-r', `
define('ABSPATH', '/');
define('ATSHIFT_CFS_URL', '/fields');
class Atshift_CFS_field {
    public $name;
    public $label;
    function get_tinymce_content_style() { return ''; }
}
function __($text, $domain) { return $text; }
function add_filter() {}
function get_current_user_id() { return 1; }
function get_user_meta() { return 'true'; }
function is_admin() { return true; }
function esc_js($value) { return addslashes($value); }
function wp_json_encode($value) { return json_encode($value); }
function atshift_cfs_capture_output($callback) {
    ob_start(); $callback(); return ob_get_clean();
}
function wp_add_inline_script($handle, $data) { echo $data; }
require $argv[1];
(new Atshift_CFS_wysiwyg())->input_head();
`, file], { encoding: 'utf8' });

// Expose the actual emitted helpers inside the test VM; production code is unchanged.
const end = '})(jQuery);';
assert.equal(script.split(end).length, 2);
new vm.Script(script);
const instrumented = script.replace(end, `
    globalThis.lifecycle = {
        restore: restoreMovedEditor,
        refresh: refreshWysiwygEditors,
        watch: watchBlockEditorMetaboxes
    };
${end}`);

function harness({ block = true, root = {} } = {}) {
    const timers = [];
    const observers = [];
    const created = [];
    const empty = {
        length: 0, on() { return this; }, find() { return this; },
        filter() { return this; }, add() { return this; }, each() { return this; },
        init_wysiwyg() { return this; }
    };
    const body = { hasClass: () => block };
    function $(value) {
        if (value === 'body') return body;
        if (value && value.jqueryStub) return value.jqueryStub;
        return empty;
    }
    $.fn = {};
    $.extend = Object.assign;
    class Editor {
        constructor(id, settings, manager) {
            Object.assign(this, { id, settings, manager, events: {}, initialized: false });
            created.push(this);
        }
        on(name, callback) { this.events[name] = callback; }
        render() { this.rendered = true; }
        setDirty(value) { this.dirty = value; }
    }
    class Observer {
        constructor(callback) { this.callback = callback; observers.push(this); }
        observe(target, options) { this.target = target; this.options = options; }
    }
    const tinyMCE = { Editor, get() { throw new Error('Unexpected editor lookup'); } };
    const context = {
        jQuery: $, tinyMCE, MutationObserver: Observer,
        document: { getElementById: () => root },
        window: { MutationObserver: Observer, setTimeout(fn) { timers.push(fn); } }
    };
    vm.runInNewContext(instrumented, context);
    return { ...context, $, empty, created, observers, timers };
}

test('an editor with its original iframe document is left untouched', () => {
    const h = harness();
    const doc = {};
    const editor = { iframeElement: { contentDocument: doc }, getDoc: () => doc };
    assert.equal(h.lifecycle.restore({}, editor), editor);
    assert.equal(h.created.length, 0);
});

for (const dirty of [false, true]) {
    test(`a replaced iframe preserves HTML/settings and dirty=${dirty}`, () => {
        const h = harness();
        const html = '<p>Text <strong>bold</strong> &amp; symbols.</p>';
        const settings = { plugins: 'link,code', wpautop: false };
        let value;
        let removed = 0;
        const textarea = {
            attr: () => 'cfs_wysiwyg_1', val: v => { value = v; }, closest: () => h.empty
        };
        const old = {
            iframeElement: { contentDocument: {} }, getDoc: () => ({}), settings,
            getContent: options => { assert.equal(options.format, 'raw'); return html; },
            isDirty: () => dirty, remove: () => { removed++; }
        };
        const replacement = h.lifecycle.restore(textarea, old);
        assert.equal(removed, 1);
        assert.equal(h.created.length, 1);
        assert.equal(value, html);
        assert.equal(replacement.id, 'cfs_wysiwyg_1');
        assert.deepEqual({ ...replacement.settings }, settings);
        assert.notEqual(replacement.settings, settings);
        assert.equal(replacement.manager, h.tinyMCE);
        assert.equal(replacement.rendered, true);
        replacement.events.init();
        assert.equal(replacement.dirty, dirty);
    });
}

test('initialization skips ready fields and block-editor staging fields', () => {
    for (const state of ['ready', 'staged']) {
        const h = harness();
        const node = { jqueryStub: {
            find: () => h.empty,
            hasClass: () => state === 'ready',
            closest: () => ({ length: state === 'staged' ? 1 : 0 }),
            addClass() { throw new Error('Should not initialize this field'); }
        } };
        h.$.fn.init_wysiwyg.call({ each(callback) { callback.call(node); } });
        assert.equal(h.created.length, 0);
    }
});

test('refresh targets Fields editors, not the standard post-content editor', () => {
    const h = harness();
    const selectors = [];
    const scope = {
        length: 1,
        find(selector) { selectors.push(selector); return h.empty; },
        filter(selector) { selectors.push(selector); return h.empty; }
    };
    h.lifecycle.refresh(scope);
    assert.deepEqual(selectors, ['.cfs_wysiwyg .wysiwyg', '.cfs_wysiwyg .wysiwyg']);
});

test('observer runs only in the block editor and coalesces relevant changes', () => {
    for (const options of [{ block: false }, { root: null }]) {
        const h = harness(options);
        h.lifecycle.watch();
        assert.equal(h.observers.length, 0);
    }
    const root = {};
    const h = harness({ root });
    h.lifecycle.watch();
    assert.equal(h.observers.length, 1);
    const observer = h.observers[0];
    assert.equal(observer.target, root);
    assert.equal(observer.options.childList, true);
    assert.equal(observer.options.subtree, true);
    observer.callback([{ addedNodes: [{ nodeType: 3 }] }]);
    assert.equal(h.timers.length, 0);
    const wrapper = { nodeType: 1, jqueryStub: { is: () => true } };
    observer.callback([{ addedNodes: [wrapper] }]);
    observer.callback([{ addedNodes: [wrapper] }]);
    assert.equal(h.timers.length, 1);
});
