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
				'Page.name' => 'ページタイトル',
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
//		'固定ページ（Page）' => array(
//			'Page.name' => 'ページタイトル',
//			'Page.description' => '説明文',
//			'Page.contents' => '記事本文',
//			'Page.draft' => '記事本文下書き',
//		),
//		'ブログ記事（Blog.BlogPost）' => array(
//			'BlogPost.name' => 'タイトル',
//			'BlogPost.content' => '記事概要',
//			'BlogPost.detail' => '記事詳細',
//			'BlogPost.content_draft' => '記事概要下書き',
//			'BlogPost.detail_draft' => '記事詳細下書き',
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
