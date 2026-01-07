<?php
/**
 * 管理画面機能
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kashiwazaki_SEO_FAQ_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . plugin_basename(KASHIWAZAKI_SEO_FAQ_PATH . 'kashiwazaki-seo-faq.php'), array($this, 'add_settings_link'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Kashiwazaki SEO FAQ',
            'Kashiwazaki SEO FAQ',
            'manage_options',
            'kashiwazaki-seo-faq',
            array($this, 'render_settings_page'),
            'dashicons-format-chat',
            81
        );
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="admin.php?page=kashiwazaki-seo-faq">' . __('設定', 'kashiwazaki-seo-faq') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings() {
        register_setting(
            'kashiwazaki_seo_faq_options_group',
            'kashiwazaki_seo_faq_options',
            array($this, 'sanitize_options')
        );

        add_settings_section(
            'kashiwazaki_seo_faq_main_section',
            __('基本設定', 'kashiwazaki-seo-faq'),
            array($this, 'render_section_info'),
            'kashiwazaki-seo-faq'
        );

        add_settings_field(
            'enable_structured_data',
            __('構造化データ（FAQPage）を有効化', 'kashiwazaki-seo-faq'),
            array($this, 'render_checkbox_field'),
            'kashiwazaki-seo-faq',
            'kashiwazaki_seo_faq_main_section',
            array(
                'id' => 'enable_structured_data',
                'label' => __('FAQブロックにJSON-LD形式の構造化データを追加します', 'kashiwazaki-seo-faq')
            )
        );

        add_settings_field(
            'default_display_type',
            __('デフォルト表示タイプ', 'kashiwazaki-seo-faq'),
            array($this, 'render_radio_field'),
            'kashiwazaki-seo-faq',
            'kashiwazaki_seo_faq_main_section',
            array(
                'id' => 'default_display_type',
                'options' => array(
                    'accordion' => __('アコーディオン型（クリックで開閉）', 'kashiwazaki-seo-faq'),
                    'simple' => __('シンプル型（常に表示）', 'kashiwazaki-seo-faq')
                )
            )
        );

        add_settings_field(
            'question_icon',
            __('質問アイコン', 'kashiwazaki-seo-faq'),
            array($this, 'render_select_field'),
            'kashiwazaki-seo-faq',
            'kashiwazaki_seo_faq_main_section',
            array(
                'id' => 'question_icon',
                'options' => array(
                    '❓' => '❓ (クエスチョンマーク絵文字)',
                    'Q' => 'Q (文字)',
                    '🤔' => '🤔 (考える絵文字)',
                    '💬' => '💬 (吹き出し絵文字)',
                    '❔' => '❔ (白いクエスチョンマーク)',
                    '？' => '？ (全角クエスチョンマーク)'
                )
            )
        );

        add_settings_field(
            'answer_icon',
            __('回答アイコン', 'kashiwazaki-seo-faq'),
            array($this, 'render_select_field'),
            'kashiwazaki-seo-faq',
            'kashiwazaki_seo_faq_main_section',
            array(
                'id' => 'answer_icon',
                'options' => array(
                    '💡' => '💡 (電球絵文字)',
                    'A' => 'A (文字)',
                    '✅' => '✅ (チェックマーク)',
                    '✔' => '✔ (チェック)',
                    '💬' => '💬 (吹き出し絵文字)',
                    '！' => '！ (全角エクスクラメーション)'
                )
            )
        );
    }

    public function sanitize_options($input) {
        // 既存のオプション値を取得してベースにする（タブ間の設定リセットを防止）
        $sanitized = get_option('kashiwazaki_seo_faq_options', array());

        // どのタブから保存されたかを判定
        // 基本設定タブには default_display_type が含まれる
        // デザイン設定タブには question_bg_color が含まれる
        $is_basic_tab = isset($input['default_display_type']);
        $is_design_tab = isset($input['question_bg_color']);

        // 基本設定タブからの保存の場合のみ、基本設定フィールドを更新
        if ($is_basic_tab) {
            $sanitized['enable_structured_data'] = isset($input['enable_structured_data']) ? true : false;

            if (in_array($input['default_display_type'], array('accordion', 'simple'))) {
                $sanitized['default_display_type'] = $input['default_display_type'];
            } else {
                $sanitized['default_display_type'] = 'accordion';
            }

            $valid_question_icons = array('❓', 'Q', '🤔', '💬', '❔', '？');
            if (isset($input['question_icon']) && in_array($input['question_icon'], $valid_question_icons)) {
                $sanitized['question_icon'] = $input['question_icon'];
            } elseif (!isset($sanitized['question_icon'])) {
                $sanitized['question_icon'] = '❓';
            }

            $valid_answer_icons = array('💡', 'A', '✅', '✔', '💬', '！');
            if (isset($input['answer_icon']) && in_array($input['answer_icon'], $valid_answer_icons)) {
                $sanitized['answer_icon'] = $input['answer_icon'];
            } elseif (!isset($sanitized['answer_icon'])) {
                $sanitized['answer_icon'] = '✅';
            }
        }

        // デザイン設定タブからの保存の場合のみ、デザイン設定フィールドを更新
        if ($is_design_tab) {
            $sanitized['question_bg_color'] = sanitize_hex_color($input['question_bg_color']) ?: '#f9fafb';
            $sanitized['question_text_color'] = isset($input['question_text_color']) ? (sanitize_hex_color($input['question_text_color']) ?: '#1f2937') : '#1f2937';
            $sanitized['answer_bg_color'] = isset($input['answer_bg_color']) ? (sanitize_hex_color($input['answer_bg_color']) ?: '#ffffff') : '#ffffff';
            $sanitized['answer_text_color'] = isset($input['answer_text_color']) ? (sanitize_hex_color($input['answer_text_color']) ?: '#4b5563') : '#4b5563';
            $sanitized['border_color'] = isset($input['border_color']) ? (sanitize_hex_color($input['border_color']) ?: '#e5e7eb') : '#e5e7eb';

            $valid_icon_sizes = array('1rem', '1.25rem', '1.5rem');
            if (isset($input['icon_size']) && in_array($input['icon_size'], $valid_icon_sizes)) {
                $sanitized['icon_size'] = $input['icon_size'];
            } elseif (!isset($sanitized['icon_size'])) {
                $sanitized['icon_size'] = '1.25rem';
            }

            $valid_font_sizes = array('0.875rem', '1rem', '1.125rem');
            if (isset($input['question_font_size']) && in_array($input['question_font_size'], $valid_font_sizes)) {
                $sanitized['question_font_size'] = $input['question_font_size'];
            } elseif (!isset($sanitized['question_font_size'])) {
                $sanitized['question_font_size'] = '1rem';
            }

            if (isset($input['answer_font_size']) && in_array($input['answer_font_size'], $valid_font_sizes)) {
                $sanitized['answer_font_size'] = $input['answer_font_size'];
            } elseif (!isset($sanitized['answer_font_size'])) {
                $sanitized['answer_font_size'] = '1rem';
            }
        }

        return $sanitized;
    }

    public function render_section_info() {
        echo '<p>' . __('Kashiwazaki SEO FAQの基本設定を行います。', 'kashiwazaki-seo-faq') . '</p>';
    }

    public function render_color_field($args) {
        $options = get_option('kashiwazaki_seo_faq_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $args['default'];
        ?>
        <input type="text"
               name="kashiwazaki_seo_faq_options[<?php echo esc_attr($args['id']); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="kashiwazaki-color-picker kashiwazaki-design-field"
               data-default-color="<?php echo esc_attr($args['default']); ?>">
        <?php
    }

    public function render_checkbox_field($args) {
        $options = get_option('kashiwazaki_seo_faq_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : false;
        ?>
        <label>
            <input type="checkbox"
                   name="kashiwazaki_seo_faq_options[<?php echo esc_attr($args['id']); ?>]"
                   value="1"
                   <?php checked($value, true); ?>>
            <?php echo esc_html($args['label']); ?>
        </label>
        <?php
    }

    public function render_radio_field($args) {
        $options = get_option('kashiwazaki_seo_faq_options');
        $value = isset($options[$args['id']]) ? $options[$args['id']] : 'accordion';

        foreach ($args['options'] as $option_value => $option_label) {
            ?>
            <label style="display: block; margin-bottom: 8px;">
                <input type="radio"
                       name="kashiwazaki_seo_faq_options[<?php echo esc_attr($args['id']); ?>]"
                       value="<?php echo esc_attr($option_value); ?>"
                       <?php checked($value, $option_value); ?>>
                <?php echo esc_html($option_label); ?>
            </label>
            <?php
        }
    }

    public function render_select_field($args) {
        $options = get_option('kashiwazaki_seo_faq_options');

        $defaults = array(
            'question_icon' => '❓',
            'answer_icon' => '✅',
            'icon_size' => '1.25rem',
            'question_font_size' => '1rem',
            'answer_font_size' => '1rem'
        );

        $default_value = isset($defaults[$args['id']]) ? $defaults[$args['id']] : '';
        $value = isset($options[$args['id']]) ? $options[$args['id']] : $default_value;
        ?>
        <select name="kashiwazaki_seo_faq_options[<?php echo esc_attr($args['id']); ?>]" class="kashiwazaki-design-field">
            <?php foreach ($args['options'] as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'kashiwazaki_seo_faq_messages',
                'kashiwazaki_seo_faq_message',
                __('設定を保存しました。', 'kashiwazaki-seo-faq'),
                'updated'
            );
        }

        settings_errors('kashiwazaki_seo_faq_messages');

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'basic';
        ?>
        <div class="wrap kashiwazaki-seo-faq-admin">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="kashiwazaki-seo-faq-tabs">
                <a href="?page=kashiwazaki-seo-faq&tab=basic"
                   class="kashiwazaki-tab <?php echo $active_tab === 'basic' ? 'active' : ''; ?>">
                    <?php _e('基本設定', 'kashiwazaki-seo-faq'); ?>
                </a>
                <a href="?page=kashiwazaki-seo-faq&tab=design"
                   class="kashiwazaki-tab <?php echo $active_tab === 'design' ? 'active' : ''; ?>">
                    <?php _e('デザイン設定', 'kashiwazaki-seo-faq'); ?>
                </a>
                <a href="?page=kashiwazaki-seo-faq&tab=howto"
                   class="kashiwazaki-tab <?php echo $active_tab === 'howto' ? 'active' : ''; ?>">
                    <?php _e('使い方', 'kashiwazaki-seo-faq'); ?>
                </a>
            </div>

            <div class="kashiwazaki-tab-content">
                <?php if ($active_tab === 'basic') : ?>
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('kashiwazaki_seo_faq_options_group');
                        do_settings_sections('kashiwazaki-seo-faq');
                        submit_button(__('設定を保存', 'kashiwazaki-seo-faq'));
                        ?>
                    </form>

                <?php elseif ($active_tab === 'design') : ?>
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('kashiwazaki_seo_faq_options_group');
                        ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('質問の背景色', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_color_field(array('id' => 'question_bg_color', 'default' => '#f9fafb')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('質問の文字色', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_color_field(array('id' => 'question_text_color', 'default' => '#1f2937')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('回答の背景色', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_color_field(array('id' => 'answer_bg_color', 'default' => '#ffffff')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('回答の文字色', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_color_field(array('id' => 'answer_text_color', 'default' => '#4b5563')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('ボーダーの色', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_color_field(array('id' => 'border_color', 'default' => '#e5e7eb')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('アイコンサイズ', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_select_field(array('id' => 'icon_size', 'options' => array('1rem' => __('小', 'kashiwazaki-seo-faq'), '1.25rem' => __('中', 'kashiwazaki-seo-faq'), '1.5rem' => __('大', 'kashiwazaki-seo-faq')))); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('質問の文字サイズ', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_select_field(array('id' => 'question_font_size', 'options' => array('0.875rem' => __('小', 'kashiwazaki-seo-faq'), '1rem' => __('中', 'kashiwazaki-seo-faq'), '1.125rem' => __('大', 'kashiwazaki-seo-faq')))); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('回答の文字サイズ', 'kashiwazaki-seo-faq'); ?></th>
                                <td><?php $this->render_select_field(array('id' => 'answer_font_size', 'options' => array('0.875rem' => __('小', 'kashiwazaki-seo-faq'), '1rem' => __('中', 'kashiwazaki-seo-faq'), '1.125rem' => __('大', 'kashiwazaki-seo-faq')))); ?></td>
                            </tr>
                        </table>

                        <h3><?php _e('プレビュー', 'kashiwazaki-seo-faq'); ?></h3>
                        <?php
                        $options = get_option('kashiwazaki_seo_faq_options');
                        $question_icon = isset($options['question_icon']) ? $options['question_icon'] : '❓';
                        $answer_icon = isset($options['answer_icon']) ? $options['answer_icon'] : '✅';
                        ?>
                        <div class="kashiwazaki-faq-preview-container">
                            <div class="kashiwazaki-faq-preview">
                                <div class="kashiwazaki-faq-item-preview">
                                    <div class="kashiwazaki-faq-question-preview">
                                        <span class="kashiwazaki-faq-icon-preview"><?php echo esc_html($question_icon); ?></span>
                                        <div class="kashiwazaki-faq-question-text-preview"><?php _e('これはサンプルの質問です', 'kashiwazaki-seo-faq'); ?></div>
                                    </div>
                                    <div class="kashiwazaki-faq-answer-preview">
                                        <span class="kashiwazaki-faq-icon-preview"><?php echo esc_html($answer_icon); ?></span>
                                        <div class="kashiwazaki-faq-answer-text-preview"><?php _e('これはサンプルの回答です。デザイン設定の変更がここにリアルタイムで反映されます。', 'kashiwazaki-seo-faq'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php submit_button(__('設定を保存', 'kashiwazaki-seo-faq')); ?>
                    </form>

                <?php else : ?>
                    <div class="kashiwazaki-howto-content">
                        <h2><?php _e('Kashiwazaki SEO FAQの使い方', 'kashiwazaki-seo-faq'); ?></h2>
                        <ol class="kashiwazaki-howto-list">
                            <li><?php _e('投稿や固定ページの編集画面で、ブロック追加ボタンから「Kashiwazaki SEO FAQ」を検索します。', 'kashiwazaki-seo-faq'); ?></li>
                            <li><?php _e('FAQブロックを追加すると、質問と回答の入力欄が表示されます。', 'kashiwazaki-seo-faq'); ?></li>
                            <li><?php _e('「+ FAQアイテムを追加」ボタンで質問と回答のペアを増やせます。', 'kashiwazaki-seo-faq'); ?></li>
                            <li><?php _e('各FAQアイテムの削除ボタンで不要なものを削除できます。', 'kashiwazaki-seo-faq'); ?></li>
                            <li><?php _e('ブロック設定（右サイドバー）で表示タイプ（アコーディオン/シンプル）を変更できます。', 'kashiwazaki-seo-faq'); ?></li>
                            <li><?php _e('アコーディオン型は質問をクリックすると回答が開閉します。シンプル型は常に表示されます。', 'kashiwazaki-seo-faq'); ?></li>
                        </ol>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function enqueue_admin_styles($hook) {
        if ('toplevel_page_kashiwazaki-seo-faq' !== $hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            'kashiwazaki-seo-faq-admin',
            KASHIWAZAKI_SEO_FAQ_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            KASHIWAZAKI_SEO_FAQ_VERSION
        );

        wp_enqueue_script(
            'kashiwazaki-seo-faq-admin-js',
            KASHIWAZAKI_SEO_FAQ_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            KASHIWAZAKI_SEO_FAQ_VERSION,
            true
        );
    }
}

Kashiwazaki_SEO_FAQ_Admin::get_instance();
