/**
 * AulaV2 FAQ accordion (frontpage mockup).
 *
 * @module theme_aulav2/faq
 */
(function() {
    'use strict';
    function init(root) {
        if (!root) {
            return;
        }
        root.addEventListener('click', function(e) {
            var item = e.target.closest('.aulav2-faq-item');
            if (!item || !root.contains(item)) {
                return;
            }
            root.querySelectorAll('.aulav2-faq-item').forEach(function(el) {
                el.classList.toggle('is-open', el === item);
            });
        });
    }
    function boot() {
        document.querySelectorAll('[data-aulav2-faq]').forEach(init);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();

