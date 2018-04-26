<?php 
class TextReplaceLogsSchema extends CakeSchema {

	public $file = 'text_replace_logs.php';

	public $connection = 'plugin';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $text_replace_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'search_pattern' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索語句', 'charset' => 'utf8'),
		'replace_pattern' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '置換後', 'charset' => 'utf8'),
		'search_regex' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '検索置換時の正規表現利用の有無'),
		'model_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '検索置換時の対象モデルID'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索置換時の対象モデル名', 'charset' => 'utf8'),
		'target_field' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索置換時の対象フィールド名', 'charset' => 'utf8'),
		'before_contents' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換前の内容', 'charset' => 'utf8'),
		'after_contents' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換後の内容', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false, 'comment' => '実行したユーザーID'),
		'query_url' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換実行時のURL', 'charset' => 'utf8'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
