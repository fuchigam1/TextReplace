<?php
/**
 * [Model] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplace extends BcPluginAppModel {
	public $name = 'TextReplace';
	public $useTable = false;
	
/**
 * Validation
 *
 * @var array
 */
	public $validate = array(
		'search_pattern' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => '検索語句を入力してください。'
			),
		)
	);
	
/**
 * フォームの初期値を設定する
 * 
 * @return array 初期値データ
 */
	public function getDefaultValue() {
		$data = array();
		$data = array(
			$this->name => array(
				'choice' => 0,
				'activate_pages' => 0,
			)
		);
		return $data;
	}
	
}
