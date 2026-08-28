(function($) {
    function parseHTML(string) {
        var tmp_html = document.implementation.createHTMLDocument();

        tmp_html.body.innerHTML = string;

        var nodes = tmp_html.body.children;
        var html = [];

        for (var i = 0, l = nodes.length; i < l; i++) {
            if ( nodes[i].tagName !== 'SCRIPT' && nodes[i].tagName !== 'STYLE' ) {
                html.push(nodes[i]);
            }
        }
        return html;
    }

    $(function() {

        $('#formzu-create-box > .hndle, #formzu-add-box > .hndle, #formzu-list-box > .hndle').hover(function() {
            $(this).find('.box-label').css('text-decoration', 'underline');
        }, function() {
            $(this).find('.box-label').css('text-decoration', 'none');
        });

        var email = formzu_ajax_obj.email;

        // Fixed on 2020.08.21
        // - open-formzu-page-buttonの処理を削除
        // Fixed on 2026.08.27
        // - emailがURLエンコードされるよう修正

        $('#goto-formzu-page-button').bind('click', function() {
            var url = 'https://www.formzu.com/new_form?email=' + encodeURIComponent(email) + '&wp-plugin';
            window.open(url, '_blank', 'noopener,noreferrer');
        });

        $('.formzu-login-button').bind('click', function() {
            var url = $(this).attr('data-url');
            var reload_form_id = $(this).attr('data-form-id');
            window.open(url, reload_form_id, 'noopener,noreferrer');
        });

        function submitFormData(form_elem_id, data) {
            console.log('submitFormData start');
            var html = data.html;
            var mobile_html = data.mobile_html;
            var form_id = data.form_id;
            var on_load_count = 0;
            var on_submit_count = 0;
            var ibody_id = 'ibody';
            var mobile_ibody_id = 'mobile_ibody';
            var submitted = false;

            // Fixed on 2020.12.08
            // - height算出用iframeのデフォルト幅を変更
            create_iframe_to_get_data(ibody_id, html, '600px');
            create_iframe_to_get_data(mobile_ibody_id, mobile_html, '320px'); //for iPhone 5

            setTimeout(function() {
                if ( submitted ) {
                    return true;
                }
                var result = execute_submit('force_submit');
                if ( ! result ) {
                    alert('更新に失敗しました。ブラウザをリロードして更新し直してください。');
                    return false;
                }
                return true;
            }, 4000);

            // Fixed on 2020.12.08
            // - iframeへのhtml追加方法を変更
            // - headタグ内に入るべき要素がbodyタグ内に入っていた問題を修正
            // - SSLスマートシールのdivタグで正しいheightが取得出来ていなかった問題を修正
            // - 画像URLの修正条件を変更
            // Fixed on 2026.08.07
            // - iframe生成時のスクリプト実行を禁止
            // - script, iframe, object, embed, base, meta[http-equiv]要素を除去
            // - イベント属性,危険性の高いURLを除去
            // Fixed on 2026.08.10
            // - attributesがundefinedの場合にエラーが発生してフォームの高さが取得できない問題を修正
            // Fixed on 2026.08.27
            // - iframeの検証を追加
            function create_iframe_to_get_data(id, html, width) {
                console.log('create_iframe_to_get_data start');
                // $('<iframe></iframe>').attr('id', id).css('width', width).css('overflow-y', 'visible').load(function() {
                $('<iframe></iframe>').attr({'id': id, 'sandbox': 'allow-same-origin'}).css('width', width).css('overflow-y', 'visible').load(function() {
                    // var $ibody = $(this.contentWindow.document.body);
                    // on_submit_count++;
                    // $ibody.append(html);
                    // $ibody = render_cross_domain_elem($ibody);

                    $(html).map(function(_, elem) {
                        if ( $(elem).is('script, iframe, object, embed, base, meta[http-equiv]') ) {
                            return;
                        }
                        $(elem).find('script, iframe, object, embed, base, meta[http-equiv]').remove();
                        $(elem).find('*').addBack().each(function() {
                            if ( this.nodeType !== 1 || ! this.attributes ) {
                                return;
                            }

                            var attributes = this.attributes;
                            for (var i = attributes.length - 1; i >= 0; i--) {
                                var name = attributes[i].name;
                                var value = attributes[i].value;

                                if ( /^on/i.test(name) ) {
                                    this.removeAttribute(name);
                                    continue;
                                }

                                if (
                                    /^(href|src|xlink:href)$/i.test(name)
                                    && /^\s*(javascript|vbscript):/i.test(value)
                                ) {
                                    this.removeAttribute(name);
                                }
                            }
                        });

                        if ( $(elem).is("meta") || $(elem).is("style") || $(elem).is("link") || $(elem).is("title") ) {
                            document.getElementById(id).contentDocument.getElementsByTagName("head")[0].appendChild(elem);
                        }
                        else {
                            $(elem).find("img").each(function() {
                                var src = $(this).attr("src");

                                if ( typeof src === 'undefined' || src === null ) {
                                    return;
                                }

                                if ( src[0] === '/' && ((src.indexOf('/userfiles') !== -1) || (src.indexOf('/image') !== -1)) ) {
                                    $(this).attr('src', 'https://ws.formzu.net' + src).each(function(){
                                        $(this).load();
                                    });
                                }
                            });
                            document.getElementById(id).contentDocument.getElementsByTagName("body")[0].appendChild(elem);
                        }
                    });

                    var iframe_document = this.contentDocument;
                    if ( ! iframe_document ) {
                        return;
                    }

                    var form_elements = iframe_document.getElementsByTagName("form");
                    if ( form_elements.length === 0 ) {
                        return;
                    }

                    var geotrust_img = document.createElement("img");
                    geotrust_img.style.cssText = "height: 62px";

                    var geotrust_elem = form_elements[0].nextElementSibling;
                    if ( ! geotrust_elem || typeof geotrust_elem === "undefined" || geotrust_elem === null ) {
                        return;
                    }

                    if ( geotrust_elem.tagName === "DIV" && geotrust_elem.id !== "advertising-bottom" ) {
                        var a = document.createElement("a");
                        geotrust_elem.appendChild(a).appendChild(geotrust_img);
                    }
                }).appendTo('body').hide();

                console.log('create_iframe_to_get_data end');
            }

            // Fixed on 2020.10.26
            // - $geo_trust_elemが指す要素が複雑になっていたのを修正
            function render_cross_domain_elem($ibody) {
                console.log('render_cross_domain_elem start');
                var $google_translate_elem = $ibody.find('#google_translate_element').css('height', '24px'); //mobile : .css('width', '100%')
                var $geo_trust_img = $('<img />').css('height', '55px');
                var $geo_trust_elem = $ibody.find('form').next();

                if ( $geo_trust_elem.prop('tagName') === 'DIV' ) {
                    $geo_trust_elem.append($('<a></a>').append($geo_trust_img));
                }
                $ibody.find('img').each(function() {
                    var src = $(this).attr('src');

                    // Added on 2020.10.21
                    if ( typeof src === 'undefined' ) {
                        return;
                    }

                    if ( src[0] === '/' && src.indexOf('/userfiles') !== -1 ) {
                        $(this).attr('src', 'https://ws.formzu.net' + src);
                        on_load_count++;
                        $(this).one('load', function() {
                            submitted = complete_loading_image();
                        }).each(function() {
                            if ( this.complete ) {
                                $(this).load();
                            }
                        });
                    }
                });
                console.log('render_cross_domain_elem end');
                return $ibody;
            }

            function complete_loading_image() {
                console.log('complete_loading_image start');
                on_load_count--;
                if ( on_load_count !== 0 ) {
                    return false;
                }
                console.log('complete_loading_image end');
                return execute_submit();
            }

            // Fixed on 2020.11.17 - 2020.12.08
            // - コードの見直し
            function execute_submit(force_submit) {
                console.log('execute_submit start');
                on_submit_count--;
                if ( on_submit_count !== 0 ) {
                    if ( ! force_submit ) {
                        return false;
                    }
                }

                var iframe = document.getElementById(ibody_id);
                var mobile_iframe = document.getElementById(mobile_ibody_id);

                if ( ! iframe || ! mobile_iframe ) {
                    return false;
                }

                $(iframe).show();
                $(mobile_iframe).show();

                var form = document.forms[form_elem_id];

                // Fixed on 2020.10.20 - 2020.10.27
                // - IE11への対応
                // - 余白算出方法の修正
                // Fixed on 2020.11.10 - 2020.12.08
                // - height算出方法の修正
                // - ブラウザ別,element別のheight補正

                var height = $(iframe.contentDocument).outerHeight(true);
                var mobile_height = $(mobile_iframe.contentDocument).outerHeight(true);

                var userAgent = window.navigator.userAgent.toLowerCase();

                if ( userAgent.indexOf("chrome") !== -1 ) {
                    height = height + 10;
                }
                else if ( userAgent.indexOf("firefox") !== -1 ) {
                    height = height + 10;
                }
                else if ( userAgent.indexOf("trident") !== -1 ) {
                    height = height + 50;
                }

                $(iframe.contentDocument).find("textarea").each(function() {
                    if ( userAgent.indexOf("chrome") !== -1 ) {
                        height = height + 17;
                    }
                    else if ( userAgent.indexOf("firefox") !== -1 ) {
                        height = height + 13;
                    }
                    else if ( userAgent.indexOf("trident") !== -1 ) {
                        height = height + 10;
                    }
                });

                // Fixed on 2020.11.18 - 2020.12.08
                // - 「必須」の表示を強調
                // - 項目ごとに改行を挿入

                var title = $(iframe.contentDocument).find('title').text();
                var items = $(iframe.contentDocument.body).find('.itemTitle').map(function(index, elem) {
                    if ( $(elem).find('.req-mark').size() ) {
                        var req_text = $(elem).find('.req-mark').first().text();
                        $(elem).find('.req-mark').remove();
                        return $(elem).text() + '[' + req_text + ']';
                    }
                    else {
                        return $(elem).text();
                    }
                }).get().join('<br />');

                form.elements['hidden_id'].value = form_id;
                form.elements['hidden_title'].value = title;
                form.elements['hidden_items'].value = items;
                form.elements['hidden_height'].value = height;
                form.elements['hidden_mobile_height'].value = mobile_height;

                $(iframe).remove();
                $(mobile_iframe).remove();
                $('.form-info-loading').remove();
                $('#' + form_elem_id).submit();
                console.log('execute_submit end');
                return true;
            }
            console.log('submitFormData end');
        }

        // Fixed on 2020.11.17
        // - コードの見直し
        function submitDefaultData(form_elem_id, form_id) {
            console.log('submitDefaultData start');
            var form = document.forms[form_elem_id];
            form.elements['hidden_id'].value = form_id;
            form.elements['hidden_title'].value = null;
            form.elements['hidden_items'].value = 'Noitems';
            form.elements['hidden_height'].value = 800;
            form.elements['hidden_mobile_height'].value = 900;

            $('#' + form_elem_id).submit();
            console.log('submitDefaultData end');
            return true;
        }

        // Fixed on 2020.10.26
        // - console.logがunreachableになっていたのを修正
        // Fixed on 2022.04.11
        // - フォームID桁数が増えた場合への対応
        // - コードの見直し
        // Fixed on 2026.08.18
        // - コードの見直し
        function getFormIdFromString(form_id) {
            console.log('getFormIdFromString start');

            if ( ! form_id ) {
                return false;
            }

            if ( form_id.match(/[Ｓｓ０-９]/g) ) {
                form_id = form_id.replace(/[Ｓｓ０-９]/g, function(s) {
                    return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
                });
            }

            form_id = form_id.trim();

            var matched_form_id = form_id.match(/S[0-9]{5,9}(?![0-9])/);
            if ( matched_form_id ) {
                form_id = matched_form_id[0];
                return form_id;
            }

            console.log('getFormIdFromString end');
            return false;
        }


        $('.formzu-reload-button').bind('click', getHTMLForReload);

        function getHTMLForReload(e, reload_form_id) {
            console.log('getHTMLForReload start');
            if ( e ) {
                e.preventDefault();
            }
            var form_id;
            var from_str;

            if ( typeof reload_form_id === 'string' ) {
                form_id = reload_form_id;
            } else {
                form_id = $(this).attr('data-form-id');
                form_id = getFormIdFromString(form_id);
            }

            if ( ! form_id ) {
                alert("フォームID : " + form_id + "\n無効な値が入力されました。正確な値を入力してください。");
                return false;
            }

            var $this = $(this);

            if ( $this.prop('tagName') ) {
                $this.unbind('click');
                $this.after('<p class="form-info-loading"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i>フォーム情報取得中...</p>');
            } else {
                $('.wrap').first().prepend('<div style="font-size: 2em;"><i class="fa fa-refresh fa-spin" aria-hidden="true"></i>フォーム情報取得中...</div>');
            }

            getHTMLByAjax('reload-form-data', form_id);
            console.log('getHTMLForReload end');
            return;
        }

        var $add_new_form = $('#add-new-form-data');

        function getFormHTMLForAdd(e) {
            console.log('getFormHTMLForAdd start');
            if ( e ) {
                e.preventDefault();
            }
            var form = document.forms['add-new-form-data'];

            if ( ! form ) {
                alert("フォームの入力値が取得できませんでした。");
                return false;
            }

            if ( ! form.elements['form_id_URL'] ) {
                alert("フォームIDが入力されていません。");
                return false;
            }

            var form_id = form.elements['form_id_URL'].value;
            form_id = getFormIdFromString(form_id);

            if ( ! form_id ) {
                alert("フォームID : " + form_id + "\n無効な値が入力されました。正確な値を入力してください。");
                return false;
            }
            $('#add-new-form-submit').unbind('click');

            var $input = $('#add-new-form-input');

            $input.unbind('keypress');
            $input.keypress(function(e) {
                e.preventDefault();
                return false;
            });
            $add_new_form.after('<p><i class="fa fa-refresh fa-spin" aria-hidden="true"></i>フォーム情報取得中...</p>');

            getHTMLByAjax('add-new-form-data', form_id);
            console.log('getFormHTMLForAdd end');
            return;
        }


        function getHTMLByAjax(form_elem_id, form_id) {
            console.log('getHTMLByAjax start');

            // Fixed on 2026.08.27
            // - 外部フォーム取得失敗時のフォールバック処理を補助関数として切り出し
            function fallbackToDefaultData() {
                alert('フォームの高さの自動設定に失敗しました。(4)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                removeLoadingElements(form_elem_id);
                submitDefaultData(form_elem_id, form_id);
                return false;
            }

            // Fixed on 2026.08.27
            // - Ajaxレスポンスの検証を追加
            function parseAjaxResponse(response, dataType) {
                console.log('parseAjaxResponse start');

                if (
                    ! (response instanceof Array)
                    || ! Array.isArray(response)
                    || response.length < 2
                    || typeof response[0] !== 'string'
                    || typeof response[1] !== 'string'
                    || response[0].trim() === ''
                    || response[1].trim() === ''
                ) {
                    return fallbackToDefaultData();
                }

                var html;
                var mobile_html;

                if ( ! $.parseHTML || typeof $.parseHTML !== 'function' ) {
                    var html = parseHTML(response[0]);
                    var mobile_html = parseHTML(response[1]);
                } else {
                    var html = $.parseHTML(response[0]);
                    var mobile_html = $.parseHTML(response[1]);
                }

                var $html = $(html);
                var $mobile_html = $(mobile_html);
                var html_has_form = (
                    $html.is('form')
                    || $html.find('form').length > 0
                );
                var mobile_html_has_form = (
                    $mobile_html.is('form')
                    || $mobile_html.find('form').length > 0
                );

                if ( ! html_has_form || ! mobile_html_has_form ) {
                    return fallbackToDefaultData();
                }

                if (
                    (response[2] instanceof Array)
                    && response[2].length > 0
                ) {
                    console.log(response[2])
                }

                /*
                if ( response[3] ) {
                    console.log(response[3]);
                }
                if ( ! html || ! mobile_html ) {
                    alert('フォームの高さの自動設定に失敗しました。(1)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                    removeLoadingElements(form_elem_id);
                    submitDefaultData(form_elem_id, form_id);
                    return false;
                }
                */

                submitFormData(form_elem_id, {
                    "form_id": form_id,
                    "html": html,
                    "mobile_html": mobile_html
                });

                console.log('parseAjaxResponse end');
            }

            function catchAjaxError(XMLHttpRequest, textStatus, error) {
                console.log('catchAjaxError start');
                console.log(XMLHttpRequest.status);
                console.log(XMLHttpRequest.responseText);
                console.log(textStatus);
                console.log(error);

                if ( XMLHttpRequest.status !== 200 ) {
                    alert('フォームの高さの自動設定に失敗しました。(2)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                    removeLoadingElements(form_elem_id);
                    submitDefaultData(form_elem_id, form_id);
                    return false;
                }

                if ( ! XMLHttpRequest.responseText ) {
                    alert('フォームの高さの自動設定に失敗しました。(3)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                    removeLoadingElements(form_elem_id);
                    submitDefaultData(form_elem_id, form_id);
                    return false;
                }

                var text = XMLHttpRequest.responseText;
                var open_index = text.indexOf('[');
                var close_index = text.lastIndexOf(']');

                if ( open_index === -1 || close_index === -1 ) {
                    alert('フォームの高さの自動設定に失敗しました。(4)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                    removeLoadingElements(form_elem_id);
                    submitDefaultData(form_elem_id, form_id);
                    return false;
                }

                var json_text = text.slice(open_index, close_index + 1);
                var json_value;

                try {
                    json_value = JSON.parse(json_text);
                } catch (e) {
                    json_value = null;
                    console.log('retry is also failed');
                }

                if ( ! (json_value instanceof Array) || ! (Array.isArray(json_value)) ) {
                    alert('フォームの高さの自動設定に失敗しました。(5)\nフォームの高さは手動で調整する必要があります。\n詳しくは「使い方」メニューの「エラー・トラブル」項目をご覧ください');
                    removeLoadingElements(form_elem_id);
                    submitDefaultData(form_elem_id, form_id);
                    return false;
                }
                console.log('catchAjaxError end');
                return parseAjaxResponse(json_value, null);
            }

            function removeLoadingElements(form_elem_id) {
                console.log('removeLoadingElements start');
                if ( form_elem_id === 'add-new-form-data' ) {
                    $('.fa-refresh').parent().remove();
                    $('#add-new-form-submit').bind('click', getFormHTMLForAdd);

                    var $input = $('#add-new-form-input');

                    $input.unbind('keypress');
                    $input.keypress(getNewFormIdFromEnterKey);

                } else if ( form_elem_id === 'reload-form-data' ) {
                    $('.fa-refresh').parent().remove();
                    $('.formzu-reload-button').bind('click', getHTMLForReload);
                }
                console.log('removeLoadingElements end');
            }

            $.ajax({
                async: true,
                type: "POST",
                url: formzu_ajax_obj.ajaxurl,
                dataType: "json",
                cache: false,
                data: {
                    "id": form_id,
                    "security": formzu_ajax_obj.nonce,
                    "action": formzu_ajax_obj.action
                },
                timeout: 30000,
                success: parseAjaxResponse,
                error: catchAjaxError
            });
            console.log('getHTMLByAjax end');
        }


        function getNewFormIdFromEnterKey(e) {
            if ( e.keyCode === 13 ) {
                e.preventDefault();
                getFormHTMLForAdd(e);
            }
        }

        $('#add-new-form-submit').bind('click', getFormHTMLForAdd);
        $('#add-new-form-input').keypress(getNewFormIdFromEnterKey);
    });
})(jQuery);
