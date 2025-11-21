<?php
/**
 * 構造化データ（FAQPage JSON-LD）機能
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kashiwazaki_SEO_FAQ_Structured_Data {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_footer', array($this, 'output_structured_data'));
    }

    public function output_structured_data() {
        $options = get_option('kashiwazaki_seo_faq_options');

        if (!isset($options['enable_structured_data']) || !$options['enable_structured_data']) {
            return;
        }

        global $post;

        if (!is_singular() || !$post) {
            return;
        }

        if (!has_block('kashiwazaki-seo-faq/faq', $post)) {
            return;
        }

        $blocks = parse_blocks($post->post_content);
        $faq_data = $this->extract_faq_data($blocks);

        if (empty($faq_data)) {
            return;
        }

        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq_data
        );

        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "\n" . '</script>' . "\n";
    }

    private function extract_faq_data($blocks) {
        $faq_data = array();

        foreach ($blocks as $block) {
            if ($block['blockName'] === 'kashiwazaki-seo-faq/faq') {
                if (isset($block['attrs']['faqs']) && is_array($block['attrs']['faqs'])) {
                    foreach ($block['attrs']['faqs'] as $faq) {
                        $question = isset($faq['question']) ? wp_strip_all_tags($faq['question']) : '';
                        $answer = isset($faq['answer']) ? wp_strip_all_tags($faq['answer']) : '';

                        if (!empty($question) && !empty($answer)) {
                            $faq_data[] = array(
                                '@type' => 'Question',
                                'name' => $question,
                                'acceptedAnswer' => array(
                                    '@type' => 'Answer',
                                    'text' => $answer
                                )
                            );
                        }
                    }
                }
            }

            if (!empty($block['innerBlocks'])) {
                $inner_faq_data = $this->extract_faq_data($block['innerBlocks']);
                $faq_data = array_merge($faq_data, $inner_faq_data);
            }
        }

        return $faq_data;
    }
}

Kashiwazaki_SEO_FAQ_Structured_Data::get_instance();
