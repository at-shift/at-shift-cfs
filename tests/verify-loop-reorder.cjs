// Requires Playwright and a disposable local WordPress with this plugin active.
// WP_PATH, WP_CLI_PATH, WP_TEST_URL, WP_TEST_USER, WP_TEST_PASSWORD are required.
// PHP_BINARY and BROWSER (chromium/webkit) are optional. Set EXPECT_BROKEN=1
// to reproduce issue #1 against an unpatched installation.
const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const path = require('node:path');
const { chromium, webkit } = require('playwright');

const required = ['WP_PATH', 'WP_CLI_PATH', 'WP_TEST_URL', 'WP_TEST_USER', 'WP_TEST_PASSWORD'];
for (const name of required) assert.ok(process.env[name], `${name} is required`);
const base = process.env.WP_TEST_URL;
assert.ok(['127.0.0.1', 'localhost'].includes(new URL(base).hostname), 'Local test server only');
const fixture = path.join(__dirname, 'fixtures/loop-reorder.php');
function wp(...args) {
    return execFileSync(process.env.PHP_BINARY || 'php', [
        process.env.WP_CLI_PATH, `--path=${process.env.WP_PATH}`, ...args
    ], { encoding: 'utf8' }).trim();
}
const data = action => JSON.parse(wp('eval-file', fixture, action));
const input = (parent, child) => `input[name="cfs[input][7][${parent}][8][${child}][9][value][]"]`;
const outer = '.field-parents > .cfs_loop';
const rows = `${outer} > .loop_wrapper`;
let browser;
let page;

(async () => {
    browser = await (process.env.BROWSER === 'webkit' ? webkit : chromium).launch({
        headless: true, executablePath: process.env.BROWSER_EXECUTABLE_PATH || undefined
    });
    page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });
    const errors = [];
    page.on('pageerror', error => { errors.push(error.message); console.error('Browser error:', error.message); });
    page.on('dialog', dialog => dialog.accept());
    await page.goto(`${base}/wp-login.php`);
    // Core moves focus after a 200 ms timer; let it finish before entering credentials.
    await page.waitForTimeout(300);
    await page.locator('#user_login').fill(process.env.WP_TEST_USER);
    await page.locator('#user_pass').fill(process.env.WP_TEST_PASSWORD);
    assert.equal(await page.locator('#user_login').inputValue(), process.env.WP_TEST_USER);
    assert.equal(await page.locator('#user_pass').inputValue(), process.env.WP_TEST_PASSWORD);
    await Promise.all([page.waitForURL(/wp-admin/), page.locator('#wp-submit').click()]);
    let post;
    async function openMetaboxes() {
        if (await page.locator('body.block-editor-page').count()) {
            const welcome = page.getByRole('dialog').getByRole('button', { name: 'Close', exact: true });
            const panel = page.getByRole('button', { name: 'Meta Boxes', exact: true });
            await welcome.or(panel).first().waitFor();
            if (await welcome.isVisible()) await welcome.click();
            await panel.waitFor();
            if (await panel.getAttribute('aria-expanded') === 'false') await panel.click();
        }
        const metabox = page.locator('.postbox').filter({ has: page.locator('.field-parents') });
        if ((await metabox.getAttribute('class')).split(' ').includes('closed')) {
            await metabox.locator('.handlediv').click();
        }
    }
    async function reset() {
        post = data('reset').post;
        await page.goto(`${base}/wp-admin/post.php?post=${post}&action=edit`);
        await openMetaboxes();
        await page.locator(rows).first().waitFor();
        await page.waitForFunction(selector => !!jQuery(selector).data('ui-sortable'), outer);
    }
    async function save() {
        if (await page.locator('body.block-editor-page').count()) {
            await page.evaluate(async () => { await wp.data.dispatch('core/editor').savePost(); });
            await page.waitForFunction(() => !wp.data.select('core/edit-post').isSavingMetaBoxes());
        } else {
            await Promise.all([page.waitForNavigation(), page.locator('#save-post').click()]);
        }
        await page.reload();
        await openMetaboxes();
        await page.locator(rows).first().waitFor();
        const saved = data('read');
        assert.equal(saved.independent[0].control_text, 'CONTROL');
        assert.match(wp('post', 'get', String(post), '--field=post_content'), /Unchanged core content/);
        return saved.parents;
    }
    // The primary reproduction uses an actual pointer drag through jQuery UI.
    async function dragOuter() {
        await page.locator(`${outer} > .loop_wrapper > .cfs_loop_head`).evaluateAll(heads => {
            for (const head of heads) {
                head.classList.remove('open');
                head.nextElementSibling.classList.remove('open');
            }
        });
        const head = page.locator(`${outer} > .loop_wrapper > .cfs_loop_head`);
        await head.nth(1).scrollIntoViewIfNeeded();
        const a = await head.nth(0).boundingBox();
        const b = await head.nth(1).boundingBox();
        await page.mouse.move(b.x + 100, b.y + b.height / 2);
        await page.mouse.down();
        await page.mouse.move(a.x + 100, a.y + 2, { steps: 20 });
        await page.mouse.up();
        await page.waitForFunction(() => document.querySelector('.field-parents > .cfs_loop > .loop_wrapper .field-parent_label input').value === 'B');
        await head.first().click();
    }
    // Additional combinations exercise the same registered sortable callback.
    async function reorder(selector, from, to) {
        await page.evaluate(({ selector, from, to }) => {
            const loop = jQuery(selector);
            const children = loop.children('.loop_wrapper');
            const item = children.eq(from);
            if (from > to) item.insertBefore(children.eq(to));
            else item.insertAfter(children.eq(to));
            loop.sortable('option', 'update').call(loop[0], jQuery.Event('sortupdate'), { item });
        }, { selector, from, to });
    }
    async function append(selector) {
        await page.locator(`${selector} > .table_footer > .cfs_add_field`).click();
    }

    await reset();
    await dragOuter();
    await append(`${rows}:nth-child(1) .field-children > .cfs_loop`);
    const added = page.locator(`${rows}:nth-child(1) .field-children > .cfs_loop > .loop_wrapper`).last().locator('.field-child_text input');
    await added.fill('B-added');
    const actualName = await added.getAttribute('name');
    if (process.env.EXPECT_BROKEN === '1') {
        assert.equal(actualName, 'cfs[input][7][1][8][2][9][value][]');
        const saved = await save();
        assert.equal(saved[0].parent_label, 'B');
        assert.equal(saved[1].children[2].child_text, 'B-added');
        console.log('REPRODUCED: added child of B was saved under A.');
        return;
    }
    assert.equal(actualName, 'cfs[input][7][0][8][2][9][value][]');
    let saved = await save();
    assert.deepEqual(saved.map(row => [row.parent_label, row.children.map(child => child.child_text)]), [
        ['B', ['B1', 'B2', 'B-added']], ['A', ['A1', 'A2']]
    ]);
    assert.equal(await page.locator(input(0, 2)).inputValue(), 'B-added');
    console.log('PASS: actual drag, child append, save, reload and CFS API readback');

    await reset();
    await reorder(outer, 1, 0);
    const inner = `${rows}:nth-child(1) .field-children > .cfs_loop`;
    await reorder(inner, 1, 0);
    const deep = `${inner} > .loop_wrapper:nth-child(1) .field-grandchildren > .cfs_loop`;
    await append(deep);
    await page.locator(`${deep} .field-grandchild_text input`).last().fill('B2-deep-added');
    saved = await save();
    assert.deepEqual(saved[0].children.map(child => child.child_text), ['B2', 'B1']);
    assert.deepEqual(saved[0].children[0].grandchildren.map(row => row.grandchild_text), ['B2-deep', 'B2-deep-added']);
    assert.equal(saved[1].children[0].grandchildren[0].grandchild_text, 'A1-deep');
    console.log('PASS: outer and inner reorder, third-level append, save and reload');

    await reset();
    // A newly added parent has an empty nested loop, but must receive an index.
    await append(outer);
    await reorder(outer, 2, 0);
    await page.locator(`${rows}:nth-child(1) .field-parent_label input`).fill('EMPTY');
    await append(`${rows}:nth-child(1) .field-children > .cfs_loop`);
    await page.locator(input(0, 0)).fill('empty-child-added');
    await append(outer);
    await page.locator(`${rows}:nth-child(4) .field-parent_label input`).fill('TAIL');
    saved = await save();
    assert.deepEqual(saved.map(row => row.parent_label), ['EMPTY', 'A', 'B', 'TAIL']);
    assert.equal(saved[0].children[0].child_text, 'empty-child-added');
    console.log('PASS: empty child loop, new parent reorder, additional parent, save and reload');

    await reset();
    await append(outer);
    // Model a parent whose only field is an empty nested loop (no named inputs).
    await page.locator(`${rows}:nth-child(3) .field-parent_label`).evaluate(node => node.remove());
    await reorder(outer, 2, 0);
    await append(`${rows}:nth-child(1) .field-children > .cfs_loop`);
    await page.locator(input(0, 0)).fill('previously-empty-parent');
    saved = await save();
    assert.deepEqual(Object.keys(saved).sort(), ['0', '1', '2']);
    assert.equal(saved[0].children[0].child_text, 'previously-empty-parent');
    assert.equal(saved[1].parent_label, 'A');
    assert.equal(saved[2].parent_label, 'B');
    console.log('PASS: a row with no named inputs retains its own nested add path');

    await reset();
    await reorder(outer, 1, 0);
    const childRows = `${inner} > .loop_wrapper`;
    await page.locator(`${childRows}:nth-child(1) > .cfs_loop_body > .cfs_loop_row_actions .cfs_loop_delete_row`).click();
    await page.locator(`${childRows}:nth-child(1) > .cfs_loop_body > .cfs_loop_row_actions .cfs_loop_insert_row`).click();
    await page.locator(`${childRows}`).last().locator('.field-child_text input').fill('B-inserted');
    await reorder(inner, 1, 0);
    await append(inner);
    await page.locator(`${childRows}`).last().locator('.field-child_text input').fill('B-after-delete');
    saved = await save();
    assert.deepEqual(saved[0].children.map(child => child.child_text), ['B-inserted', 'B2', 'B-after-delete']);
    assert.deepEqual(saved[1].children.map(child => child.child_text), ['A1', 'A2']);
    console.log('PASS: child deletion, insert-after, reorder and append remain under the intended parent');
    assert.deepEqual(errors, []);
    if (process.env.SCREENSHOT_PATH) await page.screenshot({ path: process.env.SCREENSHOT_PATH, fullPage: true });
})().catch(async error => {
    console.error(error);
    if (page && process.env.SCREENSHOT_PATH) await page.screenshot({ path: process.env.SCREENSHOT_PATH, fullPage: true });
    process.exitCode = 1;
}).finally(async () => {
    if (browser) await browser.close();
});
