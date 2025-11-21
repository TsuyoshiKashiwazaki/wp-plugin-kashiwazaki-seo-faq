/**
 * Kashiwazaki SEO FAQ - フロントエンド機能
 */

(function() {
    'use strict';

    function initFAQAccordion() {
        const accordionContainers = document.querySelectorAll('.kashiwazaki-seo-faq-accordion');

        accordionContainers.forEach(function(container) {
            const questions = container.querySelectorAll('.kashiwazaki-faq-question');

            questions.forEach(function(question) {
                question.addEventListener('click', function() {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    const answerId = this.getAttribute('aria-controls');
                    const answer = document.getElementById(answerId);

                    if (!answer) {
                        return;
                    }

                    if (isExpanded) {
                        this.setAttribute('aria-expanded', 'false');
                        answer.hidden = true;
                    } else {
                        this.setAttribute('aria-expanded', 'true');
                        answer.hidden = false;
                    }
                });

                question.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQAccordion);
    } else {
        initFAQAccordion();
    }
})();
