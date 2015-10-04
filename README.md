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
- 置換＆保存したログは「テキスト置換ログ」で確認できます。
- 設定ファイル内に、利用できないモデルの指定がなされている場合は、アラートメッセージを表示します。
  - 安全対策のため検索動作を不可にします。


## 利用上の注意
- 置換はデータベース内のテキストを直接変更するため、実行すると元に戻すことはできません。  
実施する際は、データベースのバックアップを行った上で実行することを推奨してます。


### 検索置換対象の指定
「検索置換対象の指定」とは「検索語句、置換後」の入力内容で検索・置換するデータを指定することです。

検索置換対象の指定は、任意の内容に調整できます。  
デフォルトは 固定ページ（Page）、ブログ記事（Blog.BlogPost）が選択可能対象です。  
→ /TextReplace/Config/setting.php にて設定

※サイト内検索を利用している場合、検索用データ（Content）を設定に追加して利用してください。  

内容を調整する場合、上記ファイル内の $config['TextReplace']['target'] をもとにして php ファイルを作成し、同一ディレクトリ内に配置してください。  
ファイル配置後、画面内の「検索置換対象の指定」に追加されます。

以下は作成例です。  
検索置換対象の指定に、メールフォーム設定が追加されます。

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
			// 編集画面へのリンク定義
			'edit_url' => array(
				'plugin' => 'mail',
				'controller' => 'mail_contents',
				'action' => 'edit',
				'pass' => array('id'),
			),
		),
	),
);
```

### 「検索置換対象の指定」の初期選択状態の指定
『「検索置換対象の指定」の初期選択状態の指定』とは「検索置換対象の指定」のチェックボックスにチェックが入っている状態を指定することです。

「検索置換対象の指定」のチェックボックスは、初期状態で 固定ページ と ブログ記事 が選択されている状態です。  
上記選択状態は、任意の内容に調整できます。
→ /TextReplace/Config/setting.php にて設定

内容を調整する場合、上記ファイル内の $config['TextReplace']['default_replace_target'] をもとにして php ファイルを作成し、同一ディレクトリ内に配置してください。  
ファイル配置後、「検索置換対象の指定」のチェックボックス状態が指定の内容に変わります。

以下は作成例です。  
固定ページのネーム、タイトル、ブログ記事タイトルが初期状態で選択されます。

```
<?php
/**
 * 作成ファイルサンプル: /TextReplace/Config/original.php として保存してください。
 * ファイル名は、php ファイルであれば何でも良いです。
 */
$config['TextReplace'] = array(
	// 「検索置換対象の指定」のデフォルト値
	'default_replace_target' => array(
		'Page.name',
		'Page.title',
		'BlogPost.name',
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

- 検索＆保存後、ログファイルをダウンロードする機能
- 検索結果をURLで引き渡せる仕組みができるか考える（post → get への変更）
- 検索語句の正規表現利用時、複数単語を指定した場合の検索結果・置換結果の文字列の色付け
