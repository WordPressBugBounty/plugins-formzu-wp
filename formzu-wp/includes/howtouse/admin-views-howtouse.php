<?php

if ( ! defined('FORMZU_PLUGIN_PATH') ) {
    die();
}

/*
 * Fixed at 2020.10.21
 *   - 「設定」項目を廃止
 */
function echo_how_to_use_formzu() {
?>
    <div class="wrap">
        <h2>フォームズWordPressプラグインの使い方</h2>

        <div id="poststuff" class="metabox-holder">
            <div id="post-body">
                <div class="meta-box-sortables">

                    <div class="postbox categorydiv">
                        <h3 class="hndle"><span><?php _e( '使い方', 'formzu-admin' ); ?></span></h3>
                        <div class="inside" style="margin: 0;">
                            <div id="contextual-help-back"></div>
                            <div id="contextual-help-columns">


                                <div class="contextual-help-tabs">

                                    <ul style="min-height: 230px;">
                                        <li class="tabs active">
                                            <a href="#help-tab1" area-controls="help-tab1">フォーム作成の詳しい説明</a>
                                        </li>
                                        <li class="tabs">
                                            <a href="#help-tab2" area-controls="help-tab2">固定ページ</a>
                                        </li>
                                        <li class="tabs">
                                            <a href="#help-tab3" area-controls="help-tab3">メニュー</a>
                                        </li>
                                        <li class="tabs">
                                            <a href="#help-tab4" area-controls="help-tab4">ウィジェット</a>
                                        </li>
                                        <li class="tabs">
                                            <a href="#help-tab5" area-controls="help-tab5">ショートコード</a>
                                        </li>
                                        <li class="tabs">
                                            <a href="#help-tab6" area-controls="help-tab6">テンプレート</a>
                                        </li>
                                        <!--
                                        <li class="tabs">
                                            <a href="#help-tab7" area-controls="help-tab7">設定</a>
                                        </li>
                                        -->
                                        <li class="tabs">
                                            <a href="#help-tab8" area-controls="help-tab8">エラー・トラブル</a>
                                        </li>
                                    </ul>

                                    <div class="contextual-help-sidebar">
                                        <p><strong>フォームズのページ</strong></p>
                                        <div class="formzu-help-link" href="https://www.formzu.com/" target="_blank">トップページ</div>
                                        <div class="formzu-help-link" href="https://www.formzu.com/faq" target="_blank">よくある質問</div>
                                        <div class="formzu-help-link" href="https://www.formzu.com/setup_form" target="_blank">設置方法</div>
                                        <div class="formzu-help-link" href="https://www.formzu.com/news" target="_blank">新着情報</div>
                                        <div class="formzu-help-link" onClick="javascript:window.open('https://ws.formzu.net/dist/S95904411/', 'mailform1', 'toolbar=no, location=no, status=yes, menubar=yes, resizable=yes, scrollber=yes, width=600, height=550, top=50, left=50')">改善要望</div>
                                        <div class="formzu-help-link" onClick="javascript:window.open('https://ws.formzu.net/dist/S97257136/', 'mailform1', 'toolbar=no, location=no, status=yes, menubar=yes, resizable=yes, scrollber=yes, width=600, height=550, top=50, left=50')">不具合報告</div>
                                        <script>
                                            (function($){
                                                $('.formzu-help-link').bind('click', function(){
                                                    window.open($(this).attr('href'));
                                                });
                                            })(jQuery);
                                        </script>
                                    </div>

                                </div>


                                <div id="formzu-help-tabs-wrap" class="contextual-help-tabs-wrap">
                                    <div id="help-tab1" class="metabox-holder tabs-panel help-tab-content" style="display: block;">
                                        <div class="inside inside-left">
                                            <h1>フォーム作成の詳しい説明</h1>
                                        </div>
                                        <?php echo_how_to_create_formzu_form(); ?>
                                    </div>

                                    <div id="help-tab2" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設置方法１：固定ページ</h1>
                                        </div>
                                        <?php echo_how_to_create_formzu_page(); ?>
                                    </div>

                                    <div id="help-tab3" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設置方法２：メニュー</h1>
                                        </div>
                                        <?php echo_how_to_create_formzu_menu(); ?>
                                    </div>

                                    <div id="help-tab4" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設置方法３：ウィジェット</h1>
                                        </div>
                                        <?php echo_how_to_create_formzu_widget(); ?>
                                    </div>

                                    <div id="help-tab5" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設置方法４：ショートコード</h1>
                                        </div>
                                        <?php echo_how_to_create_formzu_shortcode(); ?>

                                    </div>

                                    <div id="help-tab6" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設置方法５：テンプレート</h1>
                                        </div>
                                        <?php echo_how_to_use_formzu_template(); ?>
                                    </div>

                                    <!--
                                    <div id="help-tab7" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>設定</h1>
                                        </div>
                                        <?php //echo_about_formzu_option(); ?>
                                    </div>
                                    -->

                                    <div id="help-tab8" class="metabox-holder tabs-panel help-tab-content">
                                        <div class="inside inside-left">
                                            <h1>エラー・トラブル</h1>
                                        </div>
                                        <?php echo_about_formzu_error(); ?>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
<?php
}


function echo_how_to_create_formzu_form() {
?>
    <div class="inside inside-left">
        <p>フォームズのウェブサイト表示</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>まずは<strong>「フォーム管理」</strong>画面にある<strong>「1 フォームズへ移動して新しくフォームを作成します（別ページ）」</strong>ボタンをクリックします。</p>
            <p>すると<strong>「フォームズを表示する」</strong>ボタンと<strong>「別タブでフォームズを表示する」</strong>ボタンが表示されます。</p>
            <p>どちらのボタンも、クリックすることでフォームズのウェブサイトを表示できます。</p>
            <p>ボタンをクリックしてフォームズのウェブサイトを表示させてください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>フォーム作成準備</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>フォームズのウェブサイトが表示されたら、中央にある<strong>「フォーム作成」</strong>ボタンをクリックしてください。</p>
            <p>
                画面が変わったら、<strong>メールアドレスを確認</strong>し、<strong>フォームタイトル</strong>と<strong>パスワードを設定</strong>してください。
                <br>
                同じ画面の「個人情報の取扱いについて」と「利用規約」へ同意していただき、<strong>「同意して進む」</strong>ボタンをクリックしてください。
            </p>
            <p>フォームタイトルとパスワードに問題がなければ、新規フォーム作成画面へ移動できます。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>フォーム作成</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>新規フォーム作成画面に移動できたら、中央に表示されている指示を確認してください。</p>
            <p>以下のような指示が表示されているはずです。</p>
            <ul style="margin-left:60px;">
                <li><span>  1. 左のメインメニューの<strong>フォームを表示</strong>をクリックすると作成するフォームを確認できます。</span></li>
                <li><span>  2. 初めてフォームを作成する方は、画面左にある<strong>作成ガイド</strong>を確認してください。</span></li>
                <li><span>  3. 作成を完了するには<strong>フォーム保存</strong>を選択してください。</span></li>
            </ul>
            <p>新規フォーム作成画面、左側メニューの一番下にある<strong>「フォーム保存」</strong>ボタンをクリックし、続けて表示される<strong>「保存する」</strong>ボタンをクリックしてください。</p>
            <p>フォーム保存完了画面へ移動した後、<strong>「フォーム作成通知メール」</strong>が送信されます。</p>
            <p>フォーム作成準備で確認したメールアドレス宛てに<strong>「フォーム作成通知メール」</strong>が届いているかを確認してください。</p>
            <p>
                フォーム作成通知メールが届かない場合は、メールアドレスが間違っている可能性があります。
                <br>
                フォームズのフォーム作成画面の左側メニュー内のフォーム基本情報からメールアドレスを再度確認してください。
            </p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>フォーム作成通知メール</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>フォーム作成通知メールには作成したフォームについての情報が記載されています。</p>
            <p>その情報の中から<strong>「フォームID」</strong>を見つけて、それをコピーしてください。フォーム名と作成日時の間に書かれています。</p>
            <p>次はWordPressのサイト上でフォームを表示する準備をします。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>WordPressにフォームを登録</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressの管理画面に戻ってください。</p>
            <p>WordPress管理画面の左側メインメニュー「フォームズ」または「フォーム管理」をクリックしてください。</p>
            <p><strong>「2 フォームズで作成したフォームIDを入力してください」</strong>ボタンをクリックしてください。</p>
            <p>フォームIDの入力欄が表示されるので、フォーム作成通知メールからコピーした<strong>「フォームID」</strong>を貼り付けてください。</p>
            <p>フォームIDの入力が完了したら入力欄の右にある<strong>「取得する」</strong>ボタンをクリックしてください。</p>
            <p><strong>「3 フォーム一覧（WordPressに登録したフォーム）」</strong>をクリックしてください。展開されたリスト内に、作成したフォームが登録されていれば成功です。</p>
        </div>
    </div>

    <div class="inside inside-left" style="padding-top: 20px;">
        <p>以上でフォームを設置する準備ができました。</p>
        <p>実際にフォームを設置する方法については、現在ご覧になっている「使い方」画面の他のメニューを参照してください。</p>
        <p>より簡単な設置方法は<strong>「固定ページ」「メニュー」「ウィジェット」</strong>です。ぜひお試しください。</p>
    </div>

    <div class="inside inside-left">
        <p>更新について</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>プラグイン内のフォームに関するデータ（高さ、項目等）は自動では更新されません。</p>
            <p>フォームズのフォームの編集ページにてフォーム項目を変更した際には、当プラグイン内フォーム情報の<strong>「更新」</strong>を行ってください。</p>
            <p>（当プラグインのフォーム一覧から「編集」をクリックして編集すると、更新する手間が省けます。）</p>
            <p>更新は、フォーム一覧に表示されている各フォームごとに行います。</p>
            <p>フォーム一覧にて、各フォームにマウスカーソルを乗せると「編集」「更新」「登録解除」と表示されます。ここで「更新」が行えます。</p>
            <p>更新によって埋め込まれたフォームの高さを自動で再調整し、余計なスクロールバーが表示されてしまうのを防ぎます。</p>
        </div>
    </div>
<?php
}


function echo_how_to_create_formzu_page() {
?>
    <div class="inside inside-left">
        <p>固定ページ作成機能を使ったフォーム設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressへのフォーム登録を行っていない方は、まず<strong>「フォーム作成の詳しい説明」</strong>をご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー「フォームズ」または「フォーム管理」をクリックしてください。</p>
            <p><strong>「3 フォーム一覧（WordPressに登録したフォーム）」</strong>をクリックして登録フォームのリストを展開してください。</p>
            <p>固定ページとして設置したいフォームの行と同じ行にある<strong>「固定ページ作成」</strong>ボタンをクリックしてください。</p>
            <p>固定ページの編集画面へ移動したら、フォームを設置する固定ページのタイトルを設定してください。初期値はフォームのタイトルになっています。</p>
            <p><strong>「プレビューボタン」</strong>をクリックして、フォームが正しく設置されているかどうか確認してください。</p>
            <p>作成した固定ページにアクセスできるようリンクさせた後、プレビューボタンの近くにある<strong>「公開ボタン」</strong>をクリックして、作成した固定ページを公開してください。</p>
            <p>固定ページのリンクのさせ方がわからないという方は、現在ご覧になっている「使い方」画面の<strong>「メニュー」</strong>項目を試してみてください。</p>
            <p>固定ページ内に書かれているショートコードの設定については、現在ご覧になっている「使い方」画面の<strong>「設定」</strong>項目を参照してください。</p>
            <p><strong>注意</strong>：フォームズのフォーム編集ページにてフォーム項目を変更した際には、併せてWordPress側でフォームデータの<strong>「更新」</strong>を行ってください。</p>
            <p>（当プラグインのフォーム一覧リストから「編集」をクリックして編集すると、更新する手間が省けます。）</p>
            <p>「更新」によって埋め込まれたフォームの高さを自動で再調整し、余計なスクロールバーが表示されてしまうのを防ぎます。</p>
        </div>
    </div>
<?php
}


function echo_how_to_create_formzu_menu() {
?>
    <div class="inside inside-left">
        <p>プラグイン機能を使ったメニューへのフォームリンク設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressへのフォーム登録を行っていない方は、まず<strong>「フォーム作成の詳しい説明」</strong>をご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー<strong>「外観」</strong>をクリックしてください。</p>
            <p>次に、WordPress管理画面の左側メインメニュー「外観」の下にある<strong>「メニュー」</strong>項目をクリックしてください。</p>
            <p>管理画面右上にある「表示オプション」をクリックして、「フォームズ フォームリンク」にチェックが入っているのを確認してください。チェックが入っていない場合、クリックしてチェックを入れてください。</p>
            <p>メニューの編集画面へ移動したら、編集画面内の左側にある<strong>「フォームズ フォームリンク」</strong>項目をクリックしてください。</p>
            <p><strong>「リンクさせるフォーム」</strong>の選択欄をクリックしてください。</p>
            <p>選択欄の中からリンクさせたいフォームを選択した後、<strong>「メニューに追加」</strong>ボタンをクリックしてください。</p>
            <p>メニューの編集画面の編集画面内の中央<strong>「メニュー構造」</strong>の下に、「リンクさせるフォーム」で選択したフォームの名前がついた<strong>「フォームズ フォームリンク」</strong>項目が追加されたことを確認してください。</p>
            <p>確認ができたら、その「メニュー構造」欄の右側にある<strong>「メニューを保存」</strong>ボタンをクリックしてください。</p>
            <p>以上でフォームの設置が完了しました。</p>
            <p>実際の公開ページにアクセスして、メニュー内にフォームへのリンクがあることを確認してください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>固定ページを使ったメニューへのフォームリンク設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>まだ固定ページを作成していない方は、現在ご覧になっている「使い方」画面の<strong>「固定ページ」</strong>項目を先にご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー<strong>「外観」</strong>をクリックしてください。</p>
            <p>次に、WordPress管理画面の左側メインメニュー「外観」の下にある<strong>「メニュー」</strong>項目をクリックしてください。</p>
            <p>メニューの編集画面へ移動したら、編集画面内の左側にある<strong>「固定ページ」</strong>欄の中から、フォームを設置した固定ページを探してください。</p>
            <p>フォームを設置した固定ページをクリックしてチェックを付け、すぐ下の<strong>「メニューに追加」</strong>というボタンをクリックしてください。</p>
            <p>編集画面中央の<strong>「メニュー構造」</strong>欄にチェックを付けた固定ページの項目が追加されているのを確認してください。</p>
            <p>確認ができたら、その「メニュー構造」欄の右側にある<strong>「メニューを保存」</strong>ボタンをクリックしてください。</p>
            <p>以上でフォームの設置が完了しました。</p>
            <p>実際の公開ページにアクセスして、メニュー内にフォームが設置された固定ページへのリンクがあることを確認してください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>カスタムリンクを使ったメニューへのフォームリンク設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressへのフォーム登録を行っていない方は、まず<strong>「フォーム作成の詳しい説明」</strong>をご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー<strong>「外観」</strong>をクリックしてください。</p>
            <p>次に、WordPress管理画面の左側メインメニュー「外観」の下にある<strong>「メニュー」</strong>項目をクリックしてください。</p>
            <p>メニューの編集画面へ移動したら、編集画面内の左側にある<strong>「カスタムリンク」</strong>項目をクリックしてください。
            <p><strong>「URL」</strong>入力欄にフォームURLを入力してください。フォームURLはフォーム作成通知メールに記載されています。</p>
            <p><strong>「リンク文字列」</strong>入力欄にフォームへのリンクであることを示す文字列を入力してください。</p>
            <p>「URL」と「リンク文字列」を入力したら、すぐ下の<strong>「メニューに追加」</strong>というボタンをクリックしてください。</p>
            <p>編集画面中央の<strong>「メニュー構造」</strong>欄に新しいカスタムリンクの項目が追加されているのを確認してください。</p>
            <p>確認ができたら、その「メニュー構造」欄の右側にある<strong>「メニューを保存」</strong>ボタンをクリックしてください。</p>
            <p>以上でフォームの設置が完了しました。</p>
            <p>実際の公開ページにアクセスして、メニュー内にフォームへのリンクがあることを確認してください。</p>
        </div>
    </div>

<?php
}

/*
 * Fixed at 2020.10.21
 *   - 設定項目を移動
 */
function echo_how_to_create_formzu_widget() {
?>
    <div class="inside inside-left">
        <p>ウィジェットでのフォーム設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressへのフォーム登録を行っていない方は、まず<strong>「フォーム作成の詳しい説明」</strong>をご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー「外観」の中の「ウィジェット」項目をクリックしてください。</p>
            <p>「利用できるウィジェット」の中に<strong>「Formzu フォームズ ウィジェット」</strong>があるのを確認してください。</p>
            <p>ウィジェットを設置したい場所へ<strong>「Formzu フォームズ ウィジェット」</strong>を追加してください。</p>
            <p>設置できたらウィジェットの設定画面が開きます。</p>
            <p>まずは<strong>「リンクさせるフォーム」</strong>を設定してください。</p>
            <p>この項目にはWordPressに登録したフォームしか表示されませんので注意してください。</p>
            <p>次に<strong>「表示するタイトル」「表示するリンク文字列」</strong>を入力してください。</p>
            <p>最後に<strong>「保存」</strong>ボタンをクリックすれば設置完了です。</p>
            <p>より詳しいウィジェットの設定については、現在ご覧になっている「使い方」画面の<strong>「設定」</strong>項目を参照してください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>ウィジェット設定</p>
    </div>
    <table>
        <tbody>
            <tr>
                <th>名前</th>
                <th>意味</th>
                <th>初期値</th>
            </tr>
            <tr>
                <td>リンクさせるフォーム</td>
                <td>ウィジェットとリンクさせるフォームです。<br>フォームIDで登録したフォームだけが表示されます。</td>
                <td>最後に登録したフォーム</td>
            </tr>
            <tr>
                <td>表示するタイトル</td>
                <td>フォームの位置を示す文字列です。<br>設定しなければ何も表示されません。</td>
                <td></td>
            </tr>
            <tr>
                <td>表示するリンク文字列</td>
                <td>フォームを開くためにクリックする文字列です。<br>設定しなければ何も表示されません。</td>
                <td></td>
            </tr>
            <tr>
                <td>表示する位置</td>
                <td>ウィジェットをどこに表示させるかの画面位置です。<br>'左下'と'右下'はスクロールに追従します。</td>
                <td>通常</td>
            </tr>
            <tr>
                <td>画面を開く方式</td>
                <td>リンクをクリックした際のフォーム画面の開き方です。</td>
                <td>モーダル画面</td>
            </tr>
            <tr>
                <td>タイトルをリンクとして使う</td>
                <td>タイトル部分をリンク文字列として使用します。<br>タイトルをクリックしてフォームを開きます。</td>
                <td>×</td>
            </tr>
        </tbody>
    </table>
<?php
}

/*
 * Fixed at 2020.10.21
 *   - 設定項目を移動
 */
function echo_how_to_create_formzu_shortcode() {
?>
    <div class="inside inside-left">
        <p>ショートコードを使ったフォーム設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressへのフォーム登録を行っていない方は、まず<strong>「フォーム作成の詳しい説明」</strong>をご覧ください。</p>
            <p>WordPress管理画面の左側メインメニュー「フォームズ」または「フォーム管理」をクリックしてください。</p>
            <p><strong>「3 フォーム一覧（WordPressに登録したフォーム）」</strong>をクリックして登録フォームのリストを展開してください。</p>
            <p>設置したいフォームの行と同じ行にある<strong>「ショートコード」項目（例：[formzu form_id="...）</strong>をクリックしてコピーしてください。</p>
            <p>WordPress管理画面の左側メインメニュー<strong>「固定ページ」</strong>項目をクリックしてください。</p>
            <p>固定ページ一覧の中からフォームを設置したいページを選ぶか、左側メインメニュー「固定ページ一覧」の下にある<strong>「新規追加」</strong>項目をクリックしてください。</p>
            <p>固定ページの編集画面へ移動したら、テキストエリアに先ほどコピーしたショートコードを貼り付けてください。</p>
            <p>フォームを設置する固定ページのタイトルを設定してください。</p>
            <p><strong>「プレビューボタン」</strong>をクリックして、フォームが正しく設置されているかどうか確認してください。</p>
            <p>作成した固定ページにアクセスできるようリンクさせた後、プレビューボタンの近くにある<strong>「公開ボタン」</strong>をクリックして、作成した固定ページを公開してください。</p>
            <p>ショートコードの設定については、現在ご覧になっている「使い方」画面の<strong>「設定」</strong>項目を参照してください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>ショートコード設定（<code>[formzu 名前="値" ]</code>）</p>
    </div>
    <table>
        <tbody>
            <tr>
                <th scope="col">名前</th>
                <th scope="col">意味</th>
                <th scope="col">初期値</th>
            </tr>
            <tr>
                <td>form_id</td>
                <td>フォームIDです。S + 5～9桁の数字です。必須。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>width</td>
                <td>表示するフォームの幅です。</td>
                <td>"600"</td>
            </tr>
            <tr>
                <td>height</td>
                <td>表示するフォームの高さです。自動で設定させたい場合は消去してください。</td>
                <td>自動取得値 or "800"</td>
            </tr>
            <tr>
                <td>mobile_height</td>
                <td>モバイル（スマホ、タブレット等）で表示するフォームの高さです。自動で設定させたい場合は消去してください。</td>
                <td>自動取得値 or "900"</td>
            </tr>
            <tr>
                <td>tagname</td>
                <td>表示に使うHTML要素です。"iframe" or "a"</td>
                <td>"iframe"</td>
            </tr>
            <tr>
                <td>text</td>
                <td>リンク文字列として表示されるテキストです。<br>フォームをiframeで埋め込む際には必要ありません。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>thickbox</td>
                <td>モーダル画面で表示させるかどうかの設定です。"on" or "off"</td>
                <td>"off"</td>
            </tr>
            <tr>
                <td>new_window</td>
                <td>別ウィンドウで表示させるかどうかの設定です。"on" or "off"</td>
                <td>"off"</td>
            </tr>
            <tr>
                <td>id</td>
                <td>HTML要素のid属性を設定します。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>class</td>
                <td>HTML要素のclass属性を設定します。</td>
                <td>""</td>
            </tr>
        </tbody>
    </table>
<?php
}


function echo_how_to_use_formzu_template() {
?>
    <div class="inside inside-left">
        <p>テンプレートファイル内の任意の箇所へのフォーム設置方法</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPress管理画面の左側メインメニュー「フォームズ」または「フォーム管理」をクリックしてください。</p>
            <p><strong>「3 フォーム一覧（WordPressに登録したフォーム）」</strong>をクリックして登録フォームのリストを展開してください。</p>
            <p>設置したいフォームの行と同じ行にある<strong>「ショートコード」項目（例：[formzu form_id="...）</strong>をクリックしてコピーしてください。</p>
            <p>WordPress管理画面の左側メインメニュー<strong>「外観」</strong>をクリックしてください。</p>
            <p>次に、WordPress管理画面の左側メインメニュー「外観」の下にある<strong>「テーマの編集」</strong>項目をクリックしてください。</p>
            <p>テーマの編集画面へ移動したら、画面右側の<strong>「テンプレート」</strong>の中から、フォームを表示させたいテンプレートファイル名をクリックしてください。</p>
            <p>編集画面内のテキストエリアに表示されたコードの任意の箇所に<pre><code>&lt;?php echo do_shortcode('ここにショートコード（例：[formzu form_id="...）'); ?&gt;</pre></code>と書き込んでください。</p>
            <p>なお、あらかじめ<pre><code>&lt;?php</code></pre>と<pre><code>?&gt;</code></pre> で囲んである箇所には<pre><code>do_shortcode('ここにショートコード（例：[formzu form_id="...）');</pre></code>と直接書き込んでください。</p>
            <p></p>
            <p></p>
        </div>
    </div>
<?php
}

/*
 * Fixed at 2020.10.21
 *   - 「設定」項目の内容を「ウィジェット」「ショートコード」項目へ移動
 *   - 「設定」項目の廃止
 */
//function echo_about_formzu_option() {
?>
    <!--
    <div class="inside">
        <h2>ショートコード設定（[formzu 名前="値" ]）</h2>
    </div>
    <table>
        <tbody>
            <tr>
                <th scope="col">名前</th>
                <th scope="col">意味</th>
                <th scope="col">初期値</th>
            </tr>
            <tr>
                <td>form_id</td>
                <td>フォームIDです。S + 5～9桁の数字です。必須。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>width</td>
                <td>表示するフォームの幅です。</td>
                <td>"600"</td>
            </tr>
            <tr>
                <td>height</td>
                <td>表示するフォームの高さです。自動で設定させたい場合は消去してください。</td>
                <td>自動取得値 or "800"</td>
            </tr>
            <tr>
                <td>mobile_height</td>
                <td>モバイル（スマホ、タブレット等）で表示するフォームの高さです。自動で設定させたい場合は消去してください。</td>
                <td>自動取得値 or "900"</td>
            </tr>
            <tr>
                <td>tagname</td>
                <td>表示に使うHTML要素です。"iframe" or "a"</td>
                <td>"iframe"</td>
            </tr>
            <tr>
                <td>text</td>
                <td>リンク文字列として表示されるテキストです。<br>フォームをiframeで埋め込む際には必要ありません。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>thickbox</td>
                <td>モーダル画面で表示させるかどうかの設定です。"on" or "off"</td>
                <td>"off"</td>
            </tr>
            <tr>
                <td>new_window</td>
                <td>別ウィンドウで表示させるかどうかの設定です。"on" or "off"</td>
                <td>"off"</td>
            </tr>
            <tr>
                <td>id</td>
                <td>HTML要素のid属性を設定します。</td>
                <td>""</td>
            </tr>
            <tr>
                <td>class</td>
                <td>HTML要素のclass属性を設定します。</td>
                <td>""</td>
            </tr>
        </tbody>
    </table>

    <div class="inside">
        <h2>ウィジェット設定</h2>
    </div>
    <table>
        <tbody>
            <tr>
                <th>名前</th>
                <th>意味</th>
                <th>初期値</th>
            </tr>
            <tr>
                <td>リンクさせるフォーム</td>
                <td>ウィジェットとリンクさせるフォームです。<br>フォームIDで登録したフォームだけが表示されます。</td>
                <td>最後に登録したフォーム</td>
            </tr>
            <tr>
                <td>表示するタイトル</td>
                <td>フォームの位置を示す文字列です。<br>設定しなければ何も表示されません。</td>
                <td></td>
            </tr>
            <tr>
                <td>表示するリンク文字列</td>
                <td>フォームを開くためにクリックする文字列です。<br>設定しなければ何も表示されません。</td>
                <td></td>
            </tr>
            <tr>
                <td>表示する位置</td>
                <td>ウィジェットをどこに表示させるかの画面位置です。<br>'左下'と'右下'はスクロールに追従します。</td>
                <td>通常</td>
            </tr>
            <tr>
                <td>画面を開く方式</td>
                <td>リンクをクリックした際のフォーム画面の開き方です。</td>
                <td>モーダル画面</td>
            </tr>
            <tr>
                <td>タイトルをリンクとして使う</td>
                <td>タイトル部分をリンク文字列として使用します。<br>タイトルをクリックしてフォームを開きます。</td>
                <td>×</td>
            </tr>
        </tbody>
    </table>
    -->
<?php
//}


function echo_about_formzu_error() {
?>
    <div class="inside inside-left">
        <p>[フォームズ－エラー]というフォームが登録される</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>フォームIDの誤入力により、存在しないフォームが登録された状態です。</p>
            <p>再度フォームIDを確認した後、正しいフォームIDを入力してください。</p>
            <p>フォームIDは「S」と5桁以上の数字で構成されています。</p>
            <p>フォームのURLをそのままコピー＆ペーストして取得する方法がお手軽です。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>[フォームの高さの自動設定に失敗しました...]というメッセージが表示される</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>フォームのタイトル、高さ、項目の自動設定に失敗した状態です。</p>
            <p>新しく登録されたフォームを設置した際にスクロールバーが表示されてしまう可能性があります。</p>
            <p>スクロールバーを消すには「height（パソコン画面上での高さ」「mobile_height（スマホ上での高さ」の値をショートコード内に記述する必要があります。</p>
            <p>詳細は以降の<strong>「埋め込みフォームにスクロールバーが表示される」</strong>をご覧ください。</p>
            <br>
            <p>当プラグインではフォームを追加する前に、WordPressサーバ内でフォームについての情報を取得し、詳細な設定を行いますが、</p>
            <p>このメッセージは、フォーム情報の取得に失敗し、詳細な設定が行われなかった状態を表しています。</p>
            <p>以下のような原因が考えられます。</p>
            <ul>
                <li><span>PHPの設定 : 設定ファイル（php.ini）のallow_url_fopenがoffになっている可能性があります。レンタルサーバーなどでセキュリティ対策としてoffになっている場合があります。</span></li>
                <li><span>他のプラグインからの影響：他のプラグインのPHP出力処理と当プラグインのPHP出力処理とが競合し、正常な出力結果が得られない状態である可能性があります。</span></li>
            </ul>
            <a href="https://www.formzu.com/setup_form" target="_blank">プラグイン以外にフォーム設置する方法</a>
        </div>
    </div>

    <div class="inside inside-left">
        <p>埋め込みフォームにスクロールバーが表示される</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>ショートコード内に書かれている「height」「mobile_height」の値を調整してください。</p>
            <p>これらの値を増減させることで、ページ上でフォームが表示されるスペースを広げることができます。</p>
            <p>「height」はパソコンから見た場合のフォームの高さを調節できます。（例 height="800"</p>
            <p>「mobile_height」はスマホ・携帯から見た場合のフォームの高さを調節できます。（例 mobile_height="900"</p>
            <p>スクロールバーを消したい場合は、値を増やしてスペースを広げることで解決できます。</p>
            <p>上手くいかない場合は<a href="https://www.formzu.com/setup_form" target="_blank">フォームの設置方法</a>の「ぺージ内に埋め込む方法」もお試しください。</p>
        </div>
    </div>

    <div class="inside inside-left">
        <p>横幅（width）の設定が反映されない</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <p>WordPressプラグインでは表示上の設定のみが可能となっております。</p>
            <p>フォーム本体の横幅の設定は、フォームズのフォーム修正ページのメニュー「デザイン設定」＞「サイズ・位置・フォント」より設定できます。</p>
        </div>
    </div>
<?php
}

