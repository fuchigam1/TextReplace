<?php
/**
 * [Config] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
App::uses('TextReplaceUtil', 'TextReplace.Lib');
/**
 * システムナビ
 */
$config['BcApp.adminNavi.text_replace'] = array(
	'name'		 => 'テキスト置換 プラグイン',
	'contents'	 => array(
		array('name'	 => 'テキスト置換',
			'url'	 => array(
				'admin'		 => true,
				'plugin'	 => 'text_replace',
				'controller' => 'text_replaces',
				'action'	 => 'index')
		),
		array('name'	 => 'テキスト置換ログ',
			'url'	 => array(
				'admin'		 => true,
				'plugin'	 => 'text_replace',
				'controller' => 'text_replace_logs',
				'action'	 => 'index')
		)
	)
);
$config['BcApp.adminNavigation'] = [
	'Contents' => [
		'TextReplace' => [
			'title' => 'テキスト検索置換',
			'menus' => [
				'TextReplaces' => [
					'title' => 'テキスト検索置換処理',
					'url' => ['admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'],
				],
				'TextReplaceLogs' => [
					'title' => 'テキスト置換ログ',
					'url' => ['admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'index'],
				],
				'TextReplaceLogsDownloadCsv' => [
					'title' => 'テキスト置換ログCSVダウンロード',
					'url' => ['admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'download_csv'],
				],
			]
		],
	],
];

/**
 * TextReplace専用ログ
 */
if (!defined('LOG_TEXT_REPLACE')) {
	define('LOG_TEXT_REPLACE', 'log_text_replace');
	CakeLog::config('log_text_replace', array(
		'engine' => 'FileLog',
		'types' => array('log_text_replace'),
		'file' => 'log_text_replace',
		'size' => '5MB',
		'rotate' => 5,
	));
}
if (!defined('LOG_TEXT_REPLACE_BEFORE')) {
	define('LOG_TEXT_REPLACE_BEFORE', 'log_text_replace_before');
	CakeLog::config('log_text_replace_before', array(
		'engine' => 'FileLog',
		'types' => array('log_text_replace_before'),
		'file' => 'log_text_replace_before',
		'size' => '5MB',
		'rotate' => 5,
	));
}

/**
 * テキスト置換用設定
 *
 */
$config['TextReplace'] = array(
	// フィールドタイプ種別
	'target'				 => array(
//		設定用雛形
//		'PLUGIN_NAME.MODEL_NAME' => array(
//			'name' => 'MODEL_NAME',
//			'title' => 'MODEL_TITLE',
//			'fields' => array(
//				'MODEL_NAME.FIELD_NAME' => 'FIELD_TITLE',
//				),
//			編集画面へのリンク定義
//			'edit_url' => array(
//				'plugin' => 'plugin_name',
//				'controller' => 'controller_name',
//				'action' => 'action_name',
//				'pass' => array('paramater'),
//			),
//		),
		'Page'			 => array(
			'name'		 => 'Page',
			'title'		 => '固定ページ',
			'fields'	 => array(
				'Content.name'			 => 'ページ名',
				'Content.title'		 => 'ページタイトル',
				'Content.description'	 => '説明文',
				'Page.contents'		 => '記事本文',
				'Page.draft'		 => '記事本文下書き',
			),
			'edit_url'	 => array(
				'plugin'	 => null,
				'controller' => 'pages',
				'action'	 => 'edit',
				'pass'		 => array('id'),
			),
		),
		'Blog.BlogPost'	 => array(
			'name'		 => 'BlogPost',
			'title'		 => 'ブログ記事',
			'fields'	 => array(
				'BlogPost.name'			 => 'タイトル',
				'BlogPost.content'		 => '記事概要',
				'BlogPost.detail'		 => '記事詳細',
				'BlogPost.content_draft' => '記事概要下書き',
				'BlogPost.detail_draft'	 => '記事詳細下書き',
			),
			'edit_url'	 => array(
				'plugin'	 => 'blog',
				'controller' => 'blog_posts',
				'action'	 => 'edit',
				'pass'		 => array('blog_content_id', 'id'),
			),
		),
//		サイト内検索を利用している場合の、検索用データ保存モデル
//		'SearchIndex' => array(
//			'name' => 'SearchIndex',
//			'title' => '検索用データ',
//			'fields' => array(
//				'SearchIndex.title' => 'タイトル',
//				'SearchIndex.detail' => '記事本文',
//			),
//		),
//		'Blog.BlogContent' => array(
//			'name' => 'BlogContent',
//			'title' => 'ブログ記事',
//			'fields' => array(
//				'BlogContent.name' => 'ブログアカウント名',
//				'BlogContent.title' => 'ブログタイトル',
//				'BlogContent.description' => 'ブログ説明文',
//			),
//			'edit_url' => array(
//				'plugin' => 'blog',
//				'controller' => 'blog_contents',
//				'action' => 'edit',
//				'pass' => array('id'),
//			),
//		),
//		'Blog.BlogCategory' => array(
//			'name' => 'BlogCategory',
//			'title' => 'ブログカテゴリ',
//			'fields' => array(
//				'BlogContent.title' => 'ブログカテゴリタイトル',
//				'BlogContent.name' => 'ブログカテゴリ名',
//			),
//			'edit_url' => array(
//				'plugin' => 'blog',
//				'controller' => 'blog_categories',
//				'action' => 'edit',
//				'pass' => array('blog_content_id', 'id'),
//			),
//		),
//		'Mail.MailContent' => array(
//			'name' => 'MailContent',
//			'title' => 'メールフォーム設定',
//			'fields' => array(
//				'MailContent.name' => 'メールフォームアカウント名',
//				'MailContent.title' => 'メールフォームタイトル',
//				'MailContent.description' => 'メールフォーム説明文',
//			),
//			'edit_url' => array(
//				'plugin' => 'mail',
//				'controller' => 'mail_contents',
//				'action' => 'edit',
//				'pass' => array('id'),
//			),
//		),
	),
	// 「検索置換対象の指定」のデフォルト値
	'default_replace_target' => array(
//		'MODEL_NAME.FIELD_NAME',
		'Content.name',
		'Content.title',
		'Content.description',
		'Page.contents',
		'Page.draft',
		'BlogPost.name',
		'BlogPost.content',
		'BlogPost.detail',
		'BlogPost.content_draft',
		'BlogPost.detail_draft',
	),
);

/**
 * /Plugin/TextReplace/Config 内に置いたphpファイルの設定内容を読み込む
 *
 */
$path		 = dirname(__FILE__);
$dir		 = new Folder($path);
$files		 = $dir->find('.*\.php');
$excludeFile = array('setting.php', 'init.php');
foreach ($files as $file) {
	if (!in_array($file, $excludeFile)) {
		$original = $config;
		include $dir->pwd() . DS . $file; // $config に内容が格納される
		// 追加した設定ファイル内の独自の「検索置換対象の指定」のデフォルト値を優先する
		if (isset($config['TextReplace']['default_replace_target']) && !empty($config['TextReplace']['default_replace_target'])) {
			$original['TextReplace']['default_replace_target'] = $config['TextReplace']['default_replace_target'];
		}

		$config = Hash::merge($original, $config); // デフォルト設定とマージする
	}
}
