/**
 * Kashiwazaki SEO FAQ - 管理画面JavaScript
 */

(function($) {
    'use strict';

    function updatePreview() {
        var questionBgColor = $('input[name="kashiwazaki_seo_faq_options[question_bg_color]"]').val();
        var questionTextColor = $('input[name="kashiwazaki_seo_faq_options[question_text_color]"]').val();
        var answerBgColor = $('input[name="kashiwazaki_seo_faq_options[answer_bg_color]"]').val();
        var answerTextColor = $('input[name="kashiwazaki_seo_faq_options[answer_text_color]"]').val();
        var borderColor = $('input[name="kashiwazaki_seo_faq_options[border_color]"]').val();
        var iconSize = $('select[name="kashiwazaki_seo_faq_options[icon_size]"]').val();
        var questionFontSize = $('select[name="kashiwazaki_seo_faq_options[question_font_size]"]').val();
        var answerFontSize = $('select[name="kashiwazaki_seo_faq_options[answer_font_size]"]').val();

        if (!questionBgColor || !questionTextColor || !answerBgColor || !answerTextColor || !borderColor) {
            return;
        }

        $('.kashiwazaki-faq-item-preview').css('border-color', borderColor);

        $('.kashiwazaki-faq-question-preview').css({
            'background-color': questionBgColor,
            'color': questionTextColor,
            'font-size': questionFontSize
        });

        $('.kashiwazaki-faq-answer-preview').css({
            'background-color': answerBgColor,
            'border-top-color': borderColor
        });

        $('.kashiwazaki-faq-answer-text-preview').css({
            'color': answerTextColor,
            'font-size': answerFontSize
        });

        $('.kashiwazaki-faq-icon-preview').css('font-size', iconSize);
    }

    function initColorPickers() {
        if (typeof $.fn.wpColorPicker === 'undefined') {
            return;
        }

        $('.kashiwazaki-color-picker').wpColorPicker({
            change: function(event, ui) {
                updatePreview();
            },
            clear: function() {
                updatePreview();
            }
        });

        $('select.kashiwazaki-design-field').on('change', updatePreview);

        updatePreview();
    }

    $(document).ready(function() {
        if ($('.kashiwazaki-color-picker').length > 0) {
            initColorPickers();
        }
    });

})(jQuery);
