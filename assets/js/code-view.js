(function() {
    function copyText(text, done) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function() {
                fallbackCopy(text, done);
            });
            return;
        }

        fallbackCopy(text, done);
    }

    function fallbackCopy(text, done) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', 'readonly');
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
        }
        catch (e) {}
        document.body.removeChild(textarea);
        done();
    }

    document.addEventListener('click', function(event) {
        var button = event.target.closest ? event.target.closest('.atshift-cfs-code-view-copy') : null;
        if (!button) {
            return;
        }

        var wrapper = button.closest('.atshift-cfs-code-view');
        var code = wrapper ? wrapper.querySelector('code') : null;
        if (!code) {
            return;
        }

        copyText(code.innerText, function() {
            var label = button.getAttribute('data-label') || button.textContent;
            button.textContent = button.getAttribute('data-copied') || label;
            window.setTimeout(function() {
                button.textContent = label;
            }, 1600);
        });
    });
})();
