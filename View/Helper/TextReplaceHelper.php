<?php
/**
 * [Helper] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceHelper extends AppHelper
{
	/**
	 * ヘルパー
	 *
	 */
	public $helpers = array('BcBaser');
	
	/**
	 * 検索語句
	 * 
	 * @var string
	 */
	public $searchText = '';
	
	/**
	 * 置換後
	 * 
	 * @var string
	 */
	public $replaceText = '';
	
	/**
	 * 設定ファイルの設定値
	 * 
	 * @var array
	 */
	public static $pluginSetting = array();
	
	/**
	 * constructor
	 * 
	 * @param \View $View
	 * @param array $settings
	 */
	function __construct(\View $View, $settings = array())
	{
		parent::__construct($View, $settings);
		self::setSelfValue();
	}
	
	/**
	 * 初期設定
	 * 
	 */
	public static function setSelfValue()
	{
		self::$pluginSetting = Configure::read('TextReplace');
	}
	
	/**
	 * モデル名とタイトルのリストを取得する
	 * 
	 * @return array
	 */
	public function getModelList()
	{
		$setting = self::$pluginSetting['target'];
		$list = array();
		foreach ($setting as $model => $fieldData) {
			$list[$fieldData['name']] = $fieldData['title'];
		}
		return $list;
	}

	/**
	 * 設定ファイルのモデル別編集リンク設定をもとに、編集画面へのリンクを生成する
	 * 
	 * @param string $modelName
	 * @param array $data
	 * @return array
	 */
	public function getEditUrl($modelName, $data)
	{
		$setting = self::$pluginSetting['target'];
		$editUrlSetting = array();
		foreach ($setting as $model => $fieldData) {
			if ($fieldData['name'] == $modelName) {
				if (!empty($fieldData['edit_url'])) {
					$editUrlSetting = $fieldData['edit_url'];
					break;
				}
			}
		}

		$editUrl = array();
		$replacedPass = array();

		if ($editUrlSetting) {
			foreach ($editUrlSetting['pass'] as $key => $args) {
				$replacedPass[$key] = str_replace($args, $data[$modelName][$args], $editUrlSetting['pass'][$key]);
			}
		} else {
			return;
		}

		if ($replacedPass) {
			$editUrl = $editUrlSetting;
			unset($editUrl['pass']);
			foreach ($replacedPass as $pass) {
				$editUrl[] = $pass;
			}
		}
		return $editUrl;
	}

	/**
	 * コンテンツ内で検索語句が該当した箇所にマークを付ける
	 * 
	 * @param string $data
	 * @param string $searchText
	 * @param boolean $regexFlg
	 * @return string
	 */
	public function getBeforeSearchReplaceData($data, $searchText, $regexFlg = false)
	{
		$contents = '';
		if (!$regexFlg) {
			$contents = $this->BcBaser->mark($searchText, nl2br(h($data)));
		} else {
			$hitCount = preg_match_all($searchText, $data, $matches);
			$contents = $data;
			if ($hitCount) {
				foreach ($matches as $matche) {
					$contents = $this->BcBaser->mark($matche, nl2br(h($contents)));
				}
			}
		}
		return $contents;
	}

}
