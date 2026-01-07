<?php
/**
 * Plugin Name: Kashiwazaki SEO FAQ
 * Plugin URI: https://www.tsuyoshikashiwazaki.jp
 * Description: シンプルで使いやすいFAQブロックを提供し、FAQPage構造化データに対応したプラグインです。投稿、固定ページ、カスタム投稿でFAQを作成できます。
 * Version: 1.0.2
 * Author: 柏崎剛 (Tsuyoshi Kashiwazaki)
 * Author URI: https://www.tsuyoshikashiwazaki.jp/profile/
 * Text Domain: kashiwazaki-seo-faq
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KASHIWAZAKI_SEO_FAQ_VERSION', '1.0.2');
define('KASHIWAZAKI_SEO_FAQ_PATH', plugin_dir_path(__FILE__));
define('KASHIWAZAKI_SEO_FAQ_URL', plugin_dir_url(__FILE__));

class Kashiwazaki_SEO_FAQ {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        require_once KASHIWAZAKI_SEO_FAQ_PATH . 'includes/admin.php';
        require_once KASHIWAZAKI_SEO_FAQ_PATH . 'includes/block.php';
        require_once KASHIWAZAKI_SEO_FAQ_PATH . 'includes/structured-data.php';
    }

    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'kashiwazaki-seo-faq',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    public function activate() {
        $default_options = array(
            'enable_structured_data' => true,
            'default_display_type' => 'accordion',
            'question_icon' => '❓',
            'answer_icon' => '✅',
            'question_bg_color' => '#f9fafb',
            'question_text_color' => '#1f2937',
            'answer_bg_color' => '#ffffff',
            'answer_text_color' => '#4b5563',
            'border_color' => '#e5e7eb',
            'icon_size' => '1.25rem',
            'question_font_size' => '1rem',
            'answer_font_size' => '1rem'
        );

        if (!get_option('kashiwazaki_seo_faq_options')) {
            add_option('kashiwazaki_seo_faq_options', $default_options);
        }
    }

    public function deactivate() {
        // クリーンアップ処理が必要な場合はここに記述
    }
}

function kashiwazaki_seo_faq() {
    return Kashiwazaki_SEO_FAQ::get_instance();
}

kashiwazaki_seo_faq();
