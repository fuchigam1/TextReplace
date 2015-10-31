<?php
/**
 * [Config] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
App::uses('TextReplaceUtil', 'TextReplace.Lib');
/**
 * システムナビ
 */
$config['BcApp.adminNavi.text_replace'] = array(
		'name'		=> 'テキスト置換 プラグイン',
		'contents'	=> array(
			array('name' => 'テキスト置換',
				'url' => array(
					'admin' => true,
					'plugin' => 'text_replace',
					'controller' => 'text_replaces',
					'action' => 'index')
			),
			array('name' => 'テキスト置換ログ',
				'url' => array(
					'admin' => true,
					'plugin' => 'text_replace',
					'controller' => 'text_replace_logs',
					'action' => 'index')
			)
	)
);

/**
 * TextReplace専用ログ ※検索ログ保存機能の追加により未使用
 * 検索置換実行時にログを保存する
 */
define('LOG_TEXT_REPLACE', 'log_text_replace');
CakeLog::config('log_text_replace', array(
	'engine' => 'FileLog',
	'types' => array('log_text_replace'),
	'file' => 'log_text_replace',
));
define('LOG_TEXT_REPLACE_BEFORE', 'log_text_replace_before');
CakeLog::config('log_text_replace_before', array(
	'engine' => 'FileLog',
	'types' => array('log_text_replace_before'),
	'file' => 'log_text_replace_before',
));

/**
 * テキスト置換用設定
 * 
 */
$config['TextReplace'] = array(
	// フィールドタイプ種別
	'target' => array(
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
		'Page' => array(
			'name' => 'Page',
			'title' => '固定ページ',
			'fields' => array(
				'Page.name' => 'ページ名',
				'Page.title' => 'ページタイトル',
				'Page.description' => '説明文',
				'Page.contents' => '記事本文',
				'Page.draft' => '記事本文下書き',
				),
			'edit_url' => array(
				'plugin' => null,
				'controller' => 'pages',
				'action' => 'edit',
				'pass' => array('id'),
			),
		),
		'Blog.BlogPost' => array(
			'name' => 'BlogPost',
			'title' => 'ブログ記事',
			'fields' => array(
				'BlogPost.name' => 'タイトル',
				'BlogPost.content' => '記事概要',
				'BlogPost.detail' => '記事詳細',
				'BlogPost.content_draft' => '記事概要下書き',
				'BlogPost.detail_draft' => '記事詳細下書き',
			),
			'edit_url' => array(
				'plugin' => 'blog',
				'controller' => 'blog_posts',
				'action' => 'edit',
				'pass' => array('blog_content_id', 'id'),
			),
		),
//		サイト内検索を利用している場合の、検索用データ保存モデル
//		'Content' => array(
//			'name' => 'Content',
//			'title' => '検索用データ',
//			'fields' => array(
//				'Content.category' => 'カテゴリ',
//				'Content.title' => 'タイトル',
//				'Content.detail' => '記事本文',
//			),
//		),
//		'PageCategory' => array(
//			'name' => 'PageCategory',
//			'title' => '固定ページカテゴリ',
//			'fields' => array(
//				'PageCategory.name' => 'ページカテゴリ名',
//				'PageCategory.title' => 'ページカテゴリタイトル',
//			),
//			'edit_url' => array(
//				'plugin' => null,
//				'controller' => 'page_categories',
//				'action' => 'edit',
//				'pass' => array('id'),
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
		'Page.name',
		'Page.title',
		'Page.description',
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
$path = dirname(__FILE__);
$dir = new Folder($path);
$files = $dir->find('.*\.php');
$excludeFile = array('setting.php', 'init.php');
foreach ($files as $file) {
	if (!in_array($file, $excludeFile)) {
		$original = $config;
		include $dir->pwd() . DS . $file;	// $config に内容が格納される
		
		// 追加した設定ファイル内の独自の「検索置換対象の指定」のデフォルト値を優先する
		if (isset($config['TextReplace']['default_replace_target']) && !empty($config['TextReplace']['default_replace_target'])) {
			$original['TextReplace']['default_replace_target'] = $config['TextReplace']['default_replace_target'];
		}
		
		$config = Hash::merge($original, $config);	// デフォルト設定とマージする
	}
}
