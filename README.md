# TextReplace プラグイン #

TextReplaceプラグインは、任意のキーワードを一括で変更できるbaserCMS専用のプラグインです。

- [Summary: Wiki](https://github.com/materializing/TextReplace/wiki)


## Installation ##

1. 圧縮ファイルを解凍後、BASERCMS/app/plugins/TextReplace に配置します。
2. 管理システムのプラグイン管理に入って、表示されている テキスト置換プラグイン を有効化して下さい。


## Uses ##

- 検索語句を指定し、「検索置換対象の指定」で指定したデータを検索することができます。
- 検索語句を指定し、置換後の指定し、「検索置換対象の指定」で指定したデータを検索＆置換した結果を確認できます。
- 検索語句を指定し、置換後の指定し、「検索置換対象の指定」で指定したデータを検索＆置換した結果の中から、置換＆保存する対象を選択して実行できます。
- 置換＆保存は、置換確認の実施後に実行できます。
- 置換＆保存したログは、/app/tmp/logs/ 内に保存されます。
  - 置換前: log_text_replace_before.log
  - 置換後: log_text_replace.log
- 設定ファイル内に、利用できないモデルの指定がなされている場合は、アラートメッセージを表示します。
  - 安全対策のため検索動作を不可にします。


## 利用上の注意
- 置換はデータベース内のテキストを直接変更するため、実行すると元に戻すことはできません。  
実施する際は、データベースのバックアップを行った上で実行することを推奨してます。


### 検索置換対象の指定
「検索置換対象の指定」とは「検索語句、置換後」の入力内容で検索・置換するデータを指定することです。

検索置換対象の指定は、任意の内容に調整できます。  
デフォルトでは 固定ページ（Page）、ブログ記事（Blog.BlogPost）、検索用データ（Content）が選択可能対象となってます。  
→ /TextReplace/Config/setting.php にて設定

内容を調整する場合、上記ファイル内の $config['TextReplace']['target'] をもとにして php ファイルを作成し、同一ディレクトリ内に配置してください。  
ファイル配置後、画面内の「検索置換対象の指定」に追加されます。

以下は作成例です。

```
<?php
/**
 * 作成ファイルサンプル: /TextReplace/Config/original.php として保存してください。
 * ファイル名は、php ファイルであれば何でも良いです。
 */
$config['TextReplace'] = array(
	'target' => array(
		'Mail.MailContent' => array(
			'name' => 'MailContent',
			'title' => 'メールフォーム設定',
			'fields' => array(
				'MailContent.name' => 'メールフォーム名',
				'MailContent.title' => 'メールフォームタイトル',
				'MailContent.description' => '説明文',
			),
		),
	),
);
```


## Bug reports, Discuss, Support

- [Issue](https://github.com/materializing/TextReplace/issues)


## Thanks

- [http://basercms.net/](http://basercms.net/)
- [http://wiki.basercms.net/](http://wiki.basercms.net/)
- [http://cakephp.jp](http://cakephp.jp)
- [Semantic Versioning 2.0.0](http://semver.org/lang/ja/)


### TODO

- デフォルトで、検索置換対象の指定の固定ページとブログ記事に、対象チェックが入る
- デフォルトの検索置換対象の指定を、設定ファイルで行うことができる
- 検索語句の正規表現利用時、複数単語を指定した場合の検索結果・置換結果の文字列の色付け
- 検索結果内の置換指定選択を、モデル単位で一括指定できるチェックボックス機能
