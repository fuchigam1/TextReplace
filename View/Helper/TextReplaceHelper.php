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
		$list	 = array();
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
		$setting		 = self::$pluginSetting['target'];
		$editUrlSetting	 = array();
		foreach ($setting as $model => $fieldData) {
			if ($fieldData['name'] == $modelName) {
				if (!empty($fieldData['edit_url'])) {
					$editUrlSetting = $fieldData['edit_url'];
					break;
				}
			}
		}

		$editUrl		 = array();
		$replacedPass	 = array();

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
	 * @param string $data 検索対象データ
	 * @param string $searchText 検索語句
	 * @param boolean $regexFlg 正規表現利用の指定
	 * @return string
	 * @deprecated 1.3.0 since 1.2.2
	 */
	public function getBeforeSearchReplaceData($data, $searchText, $regexFlg = false)
	{
		$this->log(__d('baser', deprecatedMessage('メソッド：TextReplaceHelper::getBeforeSearchReplaceData()', '1.2.2', '1.3.0', 'TextReplaceUtil::getBeforeSearchReplaceData() を利用してください。')), LOG_ALERT);
		$contents = '';
		if (!$regexFlg) {
			$contents = $this->BcBaser->mark($searchText, nl2br(h($data)));
		} else {
			$hitCount	 = preg_match_all($searchText, $data, $matches);
			$contents	 = $data;
			if ($hitCount) {
				foreach ($matches as $matche) {
					$contents = $this->BcBaser->mark($matche, nl2br(h($contents)));
				}
			}
		}
		return $contents;
	}

	/**
	 * アクセス元がテキスト置換ログ画面からのURLかを確認する
	 * 
	 * @return boolean
	 */
	public function isAccessFromTextReplaceLogs()
	{
		$refererUrl = $this->request->referer();
		if (!$refererUrl) {
			return false;
		}

		$parseUrl = parse_url($refererUrl);
		if (!isset($parseUrl['path'])) {
			return false;
		}

		$parsedUrl = Router::parse($parseUrl['path']);
		if (!isset($parsedUrl['admin']) || !isset($parsedUrl['plugin']) || !isset($parsedUrl['controller']) || !isset($parsedUrl['action']) || !isset($parsedUrl['pass'][0])) {
			return false;
		}

		if ($parsedUrl['admin'] != 'admin') {
			return false;
		}
		if ($parsedUrl['plugin'] != 'text_replace') {
			return false;
		}
		if ($parsedUrl['controller'] != 'text_replace_logs') {
			return false;
		}
		if ($parsedUrl['action'] != 'admin_view') {
			return false;
		}
		if (count($parsedUrl['pass']) != 1) {
			return false;
		}

		return true;
	}

}
