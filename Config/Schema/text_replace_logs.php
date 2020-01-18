<?php
class TextReplaceLogsSchema extends CakeSchema {

	public $file = 'text_replace_logs.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
		// mediumtext, longtext 利用を考慮し、記事データを収めておくフィールドを拡張する
		$db = ConnectionManager::getDataSource($this->connection);
		$db->cacheSources = false;
		if (get_class($db) !== 'BcMysql'){
			return true ;
		}
		if (isset($event['create'])) {
			switch ($event['create']) {
				case 'textreplacelogs':
					$tableName = $db->config['prefix'] . 'text_replace_logs';
					// COMMENTは指定しておかないと消えるため
					$db->query("ALTER TABLE {$tableName} CHANGE before_contents before_contents LONGTEXT COMMENT '検索置換前の内容';");
					$db->query("ALTER TABLE {$tableName} CHANGE after_contents after_contents LONGTEXT COMMENT '検索置換後の内容';");
					break;
			}
		}
		return true;
	}

	public $text_replace_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'search_pattern' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索語句'),
		'replace_pattern' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '置換後'),
		'search_regex' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '検索置換時の正規表現利用の有無'),
		'model_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '検索置換時の対象モデルID'),
		'model' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索置換時の対象モデル名'),
		'target_field' => array('type' => 'string', 'null' => true, 'default' => null, 'comment' => '検索置換時の対象フィールド名'),
		'before_contents' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換前の内容'),
		'after_contents' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換後の内容'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false, 'comment' => '実行したユーザーID'),
		'query_url' => array('type' => 'text', 'null' => true, 'default' => null, 'comment' => '検索置換実行時のURL'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);

}
