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
			)
	)
);

/**
 * TextReplace専用ログ
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
		),
		'Content' => array(
			'name' => 'Content',
			'title' => '検索用データ',
			'fields' => array(
				'Content.category' => 'カテゴリ',
				'Content.title' => 'タイトル',
				'Content.detail' => '記事本文',
			),
		),
//		'PageCategory' => array(
//			'name' => 'PageCategory',
//			'title' => '固定ページカテゴリ',
//			'fields' => array(
//				'PageCategory.name' => 'ページカテゴリ名',
//				'PageCategory.title' => 'ページカテゴリタイトル',
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
//		),
//		'Blog.BlogCategory' => array(
//			'name' => 'BlogCategory',
//			'title' => 'ブログカテゴリ',
//			'fields' => array(
//				'BlogContent.title' => 'ブログカテゴリタイトル',
//				'BlogContent.name' => 'ブログカテゴリ名',
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
//		),
//		設定用雛形
//		'PLUGIN_NAME.MODEL_NAME' => array(
//			'name' => 'MODEL_NAME',
//			'title' => 'MODEL_TITLE',
//			'fields' => array(
//				'MODEL_NAME.FIELD_NAME' => 'FIELD_TITLE',
//				),
//		),
	),
);
/**
 * /Plugin/TextReplace/Config 内に置いたphpファイルの設定内容を読み込む
 * 
 */
$path = dirname(__FILE__);
$dir = new Folder($path);
$files = $dir->find('.*\.php');
foreach ($files as $file) {
	if ($file !== 'setting.php') {
		$original = $config;
		include $dir->pwd() . DS . $file;	// $config に内容が格納される
		$config = Hash::merge($original, $config);	// デフォルト設定とマージする
	}
}
