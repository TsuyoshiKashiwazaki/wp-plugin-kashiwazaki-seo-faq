/**
 * Kashiwazaki SEO FAQ - ブロックエディタ
 */

(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, RadioControl, Button } = wp.components;
    const { __ } = wp.i18n;
    const { createElement: el, Fragment } = wp.element;

    const defaultDisplayType = window.kashiwazakiSeoFaqData ? window.kashiwazakiSeoFaqData.defaultDisplayType : 'accordion';
    const questionIcon = window.kashiwazakiSeoFaqData ? window.kashiwazakiSeoFaqData.questionIcon : '❓';
    const answerIcon = window.kashiwazakiSeoFaqData ? window.kashiwazakiSeoFaqData.answerIcon : '✅';

    registerBlockType('kashiwazaki-seo-faq/faq', {
        title: 'Kashiwazaki SEO FAQ',
        description: __('質問と回答のFAQブロック', 'kashiwazaki-seo-faq'),
        icon: 'format-chat',
        category: 'common',
        keywords: ['faq', 'question', 'answer', '質問', '回答', 'kashiwazaki'],
        attributes: {
            faqs: {
                type: 'array',
                default: [{
                    question: '',
                    answer: ''
                }]
            },
            displayType: {
                type: 'string',
                default: defaultDisplayType
            }
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { faqs, displayType } = attributes;

            function updateFAQ(index, field, value) {
                const newFAQs = [...faqs];
                newFAQs[index][field] = value;
                setAttributes({ faqs: newFAQs });
            }

            function addFAQ() {
                const newFAQs = [...faqs, { question: '', answer: '' }];
                setAttributes({ faqs: newFAQs });
            }

            function removeFAQ(index) {
                const newFAQs = faqs.filter((_, i) => i !== index);
                if (newFAQs.length === 0) {
                    newFAQs.push({ question: '', answer: '' });
                }
                setAttributes({ faqs: newFAQs });
            }

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('表示設定', 'kashiwazaki-seo-faq'), initialOpen: true },
                        el(RadioControl, {
                            label: __('表示タイプ', 'kashiwazaki-seo-faq'),
                            selected: displayType,
                            options: [
                                { label: __('アコーディオン型（クリックで開閉）', 'kashiwazaki-seo-faq'), value: 'accordion' },
                                { label: __('シンプル型（常に表示）', 'kashiwazaki-seo-faq'), value: 'simple' }
                            ],
                            onChange: function(value) {
                                setAttributes({ displayType: value });
                            }
                        })
                    )
                ),

                el('div', { className: 'kashiwazaki-seo-faq-editor' },
                    faqs.map(function(faq, index) {
                        return el('div', {
                            key: index,
                            className: 'kashiwazaki-faq-item-editor',
                            style: {
                                marginBottom: '20px',
                                border: '1px solid #e5e7eb',
                                borderRadius: '8px',
                                padding: '20px',
                                backgroundColor: '#f9fafb'
                            }
                        },
                            el('div', {
                                style: {
                                    display: 'flex',
                                    alignItems: 'center',
                                    marginBottom: '15px'
                                }
                            },
                                el('span', {
                                    style: {
                                        fontSize: '1.25rem',
                                        marginRight: '10px'
                                    }
                                }, questionIcon),
                                el(RichText, {
                                    tagName: 'div',
                                    placeholder: __('質問を入力...', 'kashiwazaki-seo-faq'),
                                    value: faq.question,
                                    onChange: function(value) {
                                        updateFAQ(index, 'question', value);
                                    },
                                    style: {
                                        flex: '1',
                                        fontWeight: '600',
                                        fontSize: '1rem'
                                    }
                                })
                            ),

                            el('div', {
                                style: {
                                    display: 'flex',
                                    alignItems: 'flex-start',
                                    marginBottom: '15px'
                                }
                            },
                                el('span', {
                                    style: {
                                        fontSize: '1.25rem',
                                        marginRight: '10px'
                                    }
                                }, answerIcon),
                                el(RichText, {
                                    tagName: 'div',
                                    placeholder: __('回答を入力...', 'kashiwazaki-seo-faq'),
                                    value: faq.answer,
                                    onChange: function(value) {
                                        updateFAQ(index, 'answer', value);
                                    },
                                    style: {
                                        flex: '1',
                                        fontSize: '1rem'
                                    }
                                })
                            ),

                            faqs.length > 1 && el(Button, {
                                isDestructive: true,
                                onClick: function() {
                                    removeFAQ(index);
                                },
                                style: {
                                    marginTop: '10px'
                                }
                            }, __('このFAQアイテムを削除', 'kashiwazaki-seo-faq'))
                        );
                    }),

                    el(Button, {
                        isPrimary: true,
                        onClick: addFAQ,
                        style: {
                            marginTop: '15px'
                        }
                    }, __('+ FAQアイテムを追加', 'kashiwazaki-seo-faq'))
                )
            );
        },

        save: function() {
            return null;
        }
    });
})(window.wp);
