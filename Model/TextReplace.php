<?php

/**
 * [Model] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplace extends AppModel
{

	/**
	 * ModelName
	 * 
	 * @var string
	 */
	public $name = 'TextReplace';

	/**
	 * PluginName
	 * 
	 * @var string
	 */
	public $plugin = 'TextReplace';

	/**
	 * table
	 * 
	 * @var string or boolean
	 */
	public $useTable = false;

	/**
	 * DB（Tableを未使用で、モデルのバリデーションを実行するため、スキーマを定義する
	 * 
	 * @var array
	 */
	public $_schema = array(
		'search_pattern'	 => array(
			'type'	 => 'string',
			'length' => 255,
		),
		'replace_pattern'	 => array(
			'type'	 => 'string',
			'length' => 255,
		),
	);

	/**
	 * Validation
	 *
	 * @var array
	 */
	public $validate = array(
		'search_pattern'	 => array(
			'notEmpty'	 => array(
				'rule'		 => array('notEmpty'),
				'message'	 => '検索語句を入力してください。'
			),
			'maxLength'	 => array(
				'rule'		 => array('maxLength', 255),
				'message'	 => '検索語句は255文字以内で入力してください。'
			),
		),
		'replace_pattern'	 => array(
			'maxLength' => array(
				'rule'		 => array('maxLength', 255),
				'message'	 => '置換後文字列は255文字以内で入力してください。'
			),
		),
	);

	/**
	 * フォームの初期値を設定する
	 * 
	 * @return array 初期値データ
	 */
	public function getDefaultValue()
	{
		$data = array(
			$this->name => array(
				'choice'		 => 0,
				'activate_pages' => 0,
			)
		);
		return $data;
	}

}
