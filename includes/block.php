<?php
/**
 * ブロックエディタ統合
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kashiwazaki_SEO_FAQ_Block {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    public function register_block() {
        register_block_type('kashiwazaki-seo-faq/faq', array(
            'render_callback' => array($this, 'render_block'),
            'attributes' => array(
                'faqs' => array(
                    'type' => 'array',
                    'default' => array(
                        array(
                            'question' => '',
                            'answer' => ''
                        )
                    )
                ),
                'displayType' => array(
                    'type' => 'string',
                    'default' => 'accordion'
                )
            )
        ));
    }

    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'kashiwazaki-seo-faq-block',
            KASHIWAZAKI_SEO_FAQ_URL . 'build/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            KASHIWAZAKI_SEO_FAQ_VERSION,
            true
        );

        $options = get_option('kashiwazaki_seo_faq_options');
        wp_localize_script('kashiwazaki-seo-faq-block', 'kashiwazakiSeoFaqData', array(
            'defaultDisplayType' => isset($options['default_display_type']) ? $options['default_display_type'] : 'accordion',
            'questionIcon' => isset($options['question_icon']) ? $options['question_icon'] : '❓',
            'answerIcon' => isset($options['answer_icon']) ? $options['answer_icon'] : '✅'
        ));
    }

    public function enqueue_frontend_assets() {
        if (!has_block('kashiwazaki-seo-faq/faq')) {
            return;
        }

        wp_enqueue_style(
            'kashiwazaki-seo-faq-frontend',
            KASHIWAZAKI_SEO_FAQ_URL . 'assets/css/frontend.css',
            array(),
            KASHIWAZAKI_SEO_FAQ_VERSION
        );

        $options = get_option('kashiwazaki_seo_faq_options');
        $question_bg_color = isset($options['question_bg_color']) ? $options['question_bg_color'] : '#f9fafb';
        $question_text_color = isset($options['question_text_color']) ? $options['question_text_color'] : '#1f2937';
        $answer_bg_color = isset($options['answer_bg_color']) ? $options['answer_bg_color'] : '#ffffff';
        $answer_text_color = isset($options['answer_text_color']) ? $options['answer_text_color'] : '#4b5563';
        $border_color = isset($options['border_color']) ? $options['border_color'] : '#e5e7eb';
        $icon_size = isset($options['icon_size']) ? $options['icon_size'] : '1.25rem';
        $question_font_size = isset($options['question_font_size']) ? $options['question_font_size'] : '1rem';
        $answer_font_size = isset($options['answer_font_size']) ? $options['answer_font_size'] : '1rem';

        $custom_css = "
            .kashiwazaki-faq-item {
                border-color: {$border_color};
            }
            .kashiwazaki-faq-icon {
                font-size: {$icon_size} !important;
            }
            .kashiwazaki-seo-faq-accordion .kashiwazaki-faq-question {
                background-color: {$question_bg_color};
                color: {$question_text_color};
                font-size: {$question_font_size} !important;
            }
            .kashiwazaki-seo-faq-accordion .kashiwazaki-faq-answer-text {
                color: {$answer_text_color};
                font-size: {$answer_font_size} !important;
            }
            .kashiwazaki-seo-faq-accordion .kashiwazaki-faq-answer {
                border-top-color: {$border_color};
            }
            .kashiwazaki-seo-faq-simple .kashiwazaki-faq-question {
                background-color: {$question_bg_color};
                color: {$question_text_color};
                font-size: {$question_font_size} !important;
            }
            .kashiwazaki-seo-faq-simple .kashiwazaki-faq-answer {
                background-color: {$answer_bg_color};
                border-top-color: {$border_color};
            }
            .kashiwazaki-seo-faq-simple .kashiwazaki-faq-answer-text {
                color: {$answer_text_color};
                font-size: {$answer_font_size} !important;
            }
        ";

        wp_add_inline_style('kashiwazaki-seo-faq-frontend', $custom_css);

        wp_enqueue_script(
            'kashiwazaki-seo-faq-frontend',
            KASHIWAZAKI_SEO_FAQ_URL . 'assets/js/frontend.js',
            array(),
            KASHIWAZAKI_SEO_FAQ_VERSION,
            true
        );
    }

    public function render_block($attributes) {
        $faqs = isset($attributes['faqs']) ? $attributes['faqs'] : array();
        $display_type = isset($attributes['displayType']) ? $attributes['displayType'] : 'accordion';

        if (empty($faqs)) {
            return '';
        }

        $options = get_option('kashiwazaki_seo_faq_options');
        $question_icon = isset($options['question_icon']) ? $options['question_icon'] : '❓';
        $answer_icon = isset($options['answer_icon']) ? $options['answer_icon'] : '✅';

        $output = '<div class="kashiwazaki-seo-faq kashiwazaki-seo-faq-' . esc_attr($display_type) . '">';

        foreach ($faqs as $index => $faq) {
            $question = isset($faq['question']) ? $faq['question'] : '';
            $answer = isset($faq['answer']) ? $faq['answer'] : '';

            if (empty($question) && empty($answer)) {
                continue;
            }

            $faq_id = 'kashiwazaki-faq-' . uniqid();

            $output .= '<div class="kashiwazaki-faq-item">';

            if ($display_type === 'accordion') {
                $output .= '<button class="kashiwazaki-faq-question" aria-expanded="false" aria-controls="' . esc_attr($faq_id) . '">';
                $output .= '<span class="kashiwazaki-faq-icon">' . esc_html($question_icon) . '</span>';
                $output .= '<span class="kashiwazaki-faq-question-text">' . wp_kses_post($question) . '</span>';
                $output .= '<span class="kashiwazaki-faq-toggle" aria-hidden="true">▼</span>';
                $output .= '</button>';
                $output .= '<div class="kashiwazaki-faq-answer" id="' . esc_attr($faq_id) . '" hidden>';
                $output .= '<div class="kashiwazaki-faq-answer-inner">';
                $output .= '<span class="kashiwazaki-faq-icon">' . esc_html($answer_icon) . '</span>';
                $output .= '<div class="kashiwazaki-faq-answer-text">' . wp_kses_post($answer) . '</div>';
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<div class="kashiwazaki-faq-question">';
                $output .= '<span class="kashiwazaki-faq-icon">' . esc_html($question_icon) . '</span>';
                $output .= '<div class="kashiwazaki-faq-question-text">' . wp_kses_post($question) . '</div>';
                $output .= '</div>';
                $output .= '<div class="kashiwazaki-faq-answer">';
                $output .= '<span class="kashiwazaki-faq-icon">' . esc_html($answer_icon) . '</span>';
                $output .= '<div class="kashiwazaki-faq-answer-text">' . wp_kses_post($answer) . '</div>';
                $output .= '</div>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}

Kashiwazaki_SEO_FAQ_Block::get_instance();
