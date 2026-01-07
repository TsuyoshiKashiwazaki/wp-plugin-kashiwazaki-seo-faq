=== Kashiwazaki SEO FAQ ===
Contributors: tkashiwazaki
Tags: faq, schema, structured-data, block, gutenberg
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

シンプルで使いやすいFAQブロックを提供し、FAQPage構造化データに対応したプラグインです。

== Description ==

Kashiwazaki SEO FAQは、投稿、固定ページ、カスタム投稿でFAQを作成できるWordPressプラグインです。

= 主な機能 =

* ブロックエディタ完全対応
* 2つの表示タイプ（アコーディオン型/シンプル型）
* FAQPage構造化データ（JSON-LD）対応
* カスタマイズ可能なデザイン設定
* 質問・回答アイコンの選択機能
* 1,677万色カラーピッカー
* リアルタイムプレビュー機能

= 表示タイプ =

**アコーディオン型**
質問をクリックすると回答が開閉します。

**シンプル型**
質問と回答が常に表示されます。

= デザインのカスタマイズ =

管理画面から以下をカスタマイズできます：

* 質問の背景色・文字色
* 回答の背景色・文字色
* ボーダーの色
* アイコンサイズ（小/中/大）
* 文字サイズ（小/中/大）

すべての設定はリアルタイムプレビューで確認できます。

== Installation ==

1. プラグインファイルを `/wp-content/plugins/kashiwazaki-seo-faq` ディレクトリにアップロード
2. WordPress管理画面の「プラグイン」メニューからプラグインを有効化
3. 「Kashiwazaki SEO FAQ」メニューから設定を行う

== Frequently Asked Questions ==

= ブロックエディタ以外でも使えますか？ =

いいえ、このプラグインはブロックエディタ専用です。

= 構造化データは自動的に出力されますか？ =

はい、設定で有効化すれば、FAQブロックを含むページに自動的にJSON-LD形式の構造化データが出力されます。

= デザインはカスタマイズできますか？ =

はい、管理画面のデザイン設定から色やサイズを自由にカスタマイズできます。

== Screenshots ==

1. FAQブロックの編集画面
2. アコーディオン型の表示例
3. シンプル型の表示例
4. 管理画面 - 基本設定
5. 管理画面 - デザイン設定

== Changelog ==

= 1.0.1 =
* 設定保存時のバグを修正（基本設定とデザイン設定を行き来すると片方の設定がリセットされる問題）
* フォントサイズのCSS優先度を強化（各種テーマとの互換性向上）

= 1.0.0 =
* 初回リリース

== Upgrade Notice ==

= 1.0.1 =
設定保存のバグ修正とCSS互換性の改善を行いました。

= 1.0.0 =
初回リリースです。
