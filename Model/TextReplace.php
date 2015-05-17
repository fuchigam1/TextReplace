<?php
/**
 * [Model] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplace extends BcPluginAppModel
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
	public function getDefaultValue()
	{
		$data = array(
			$this->name => array(
				'choice' => 0,
				'activate_pages' => 0,
			)
		);
		return $data;
	}
	
}
