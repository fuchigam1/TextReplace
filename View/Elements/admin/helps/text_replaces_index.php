<?php
/**
 * [ADMIN] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
?>
<p>置換はデータベース内のテキストを直接変更するため、実行すると元に戻すことはできません。</p>
<p>実施する際は、データベースのバックアップを行った上で実行することを推奨してます。</p>
<br />

<h2>検索置換対象の指定</h2>
<p>「検索置換対象の指定」とは「検索語句、置換後」の入力内容で検索・置換するデータを指定することです。</p>
<p>検索置換対象の指定は、任意の内容に調整できます。<br />
デフォルトでは 固定ページ（Page）、ブログ記事（Blog.BlogPost）、検索用データ（Content）が選択可能対象となってます。<br />
→ /TextReplace/Config/setting.php にて設定
</p>
<p>内容を調整する場合、上記ファイル内の $config['TextReplace']['target'] をもとにして php ファイルを作成し、同一ディレクトリ内に配置してください。<br />
ファイル配置後、画面内の「検索置換対象の指定」に追加されます。
</p>
