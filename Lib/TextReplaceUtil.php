<?php

/**
 * [Lib] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceUtil extends CakeObject
{

	/**
	 * 検索置換利用可能なモデル一覧
	 * 
	 * @var array
	 */
	private static $enabledModelList = array();

	/**
	 * 検索置換利用不可のモデル一覧
	 * 
	 * @var array
	 */
	private static $disabledModelList = array();

	/**
	 * 初期化処理
	 * 
	 * @param array $setting
	 */
	public static function init($setting = array())
	{
		self::getUseModel($setting);
	}

	/**
	 * getUseModel
	 * 設定ファイルから利用モデル一覧を取得する
	 * 
	 * @param array $setting
	 * @return array
	 */
	public static function getUseModel($setting = array())
	{
		$useModel = array();
		foreach ($setting as $fieldData) {
			foreach($fieldData['fields'] as $key => $field) {
				list($model) = explode('.', $key);
				if(!in_array($model, $useModel)) {
					$useModel[] = $model;	
				}
			}
		}
		self::setHandleModel($useModel);
	}

	/**
	 * 検索置換取扱い可能なモデルと不可のモデルを設定する
	 * 
	 * @param array $useModel
	 * @return boolean
	 */
	public static function setHandleModel($useModel)
	{
		if (!is_array($useModel)) {
			return false;
		}
		
		$PluginModel = ClassRegistry::init('Plugin');

		foreach ($useModel as $model) {
			if (strpos($model, '.') === false) {
				self::$enabledModelList[] = $model;
				continue;
			}

			list($pluginName, $pluginModelName) = explode('.', $model);
			$conditions = array(
				'name'	 => $pluginName,
				'status' => true,
			);
			if ($PluginModel->find('count', ['conditions' => $conditions])) {
				self::$enabledModelList[] = $model;
			} else {
				self::$disabledModelList[] = $model;
			}
		}
	}

	/**
	 * 検索置換利用可能なモデル一覧を取得する
	 * 
	 * @return array
	 */
	public static function getEnabledModel()
	{
		return self::$enabledModelList;
	}

	/**
	 * 検索置換利用不可のモデル一覧を取得する
	 * 
	 * @return array
	 */
	public static function getDisabledModel()
	{
		return self::$disabledModelList;
	}

	/**
	 * getReplaceTarget
	 * 設定ファイルから検索置換対象の指定一覧を取得する
	 * 
	 * @param array $setting
	 * @return array
	 */
	public static function getReplaceTarget($setting = array())
	{
		$replaceTarget = array();
		foreach ($setting as $model => $fieldData) {
			$keyName				 = '対象：' . $setting[$model]['title'] . '（' . $model . '）';
			$replaceTarget[$keyName] = $fieldData['fields'];
		}
		return $replaceTarget;
	}

	/**
	 * getUseModelName
	 * 設定ファイルから利用モデル名一覧を取得する
	 * 
	 * @param array $setting
	 * @return array
	 */
	public static function getUseModelName()
	{
		$useModel		 = array();
		$enabledModel	 = self::$enabledModelList;
		foreach ($enabledModel as $model) {
			if (strpos($model, '.') === false) {
				$useModel[] = $model;
				continue;
			}

			list($pluginName, $pluginModelName) = explode('.', $model);
			$useModel[] = $pluginModelName;
		}

		return $useModel;
	}

	/**
	 * getModelField
	 * 設定ファイルからモデルとフィールドの対応表を取得する
	 * 
	 * @param array $setting
	 * @return array
	 */
	public static function getModelField($setting = array())
	{
		$data = array();
		foreach ($setting as $model => $fieldData) {
			$fieldName = array();
			foreach ($fieldData['fields'] as $key => $value) {
				list($model, $field)  = explode('.', $key);
				$data[$model][] = $field;
			}
		}
		return $data;
	}

	/**
	 * getFieldTitle
	 * モデル名とフィールド名から、テキスト置換用設定内のフィールドのタイトルを取得する
	 * 
	 * @param string $targetModelName
	 * @param string $targetFieldName
	 * @return string
	 */
	public static function getFieldTitle($targetModelName = '', $targetFieldName = '')
	{
		if (!$targetModelName || !$targetFieldName) {
			return '';
		}
		$setting	 = Configure::read('TextReplace.target');
		$fieldTitle	 = '';

		foreach ($setting as $settingKey => $fieldData) {
			foreach($fieldData['fields'] as $fieldName => $title) {
				list($model, $field) = explode('.', $fieldName);
				if($model === $targetModelName && $field === $targetFieldName) {
					$fieldTitle = $title;
					break;
				}
			}
		}

		return $fieldTitle;
	}

	/**
	 * コンテンツ内で検索語句が該当した箇所にマークを付ける
	 * 
	 * @param string $data 検索対象データ
	 * @param string $searchText 検索語句
	 * @param string $replaceText 置換後文字列
	 * @param array $options オプション
	 * @return string 検索置換後データ
	 */
	public static function getBeforeSearchReplaceData($data = '', $searchText = '', $replaceText = '', $options = array())
	{
		$_options	 = array(
			'search_regex'	 => false,
			'query'			 => array(),
		);
		$options	 = Hash::merge($_options, $options);

		if ($options['search_regex']) {
			preg_match_all($searchText, $data, $matches);
			$matches = array_filter($matches);
			if ($matches) {
				$matchList		 = $matches[0];
				$highlightList	 = array();
				foreach ($matchList as $match) {
					$highlightList[] = '<strong>' . $match . '</strong>';
				}
				$data = str_replace($matchList, $highlightList, $data);
			}
		} else {
			$data = str_replace($searchText, '<strong>' . $replaceText . '</strong>', $data);
		}

		return $data;
	}

	/**
	 * 検索語句を置換後で置換する
	 * 
	 * @param string $data 検索対象データ
	 * @param string $searchText 検索語句
	 * @param string $replaceText 置換後文字列
	 * @param array $options オプション
	 * @return string 検索置換後データ
	 */
	public static function getReplaceData($data = '', $searchText = '', $replaceText = '', $options = array())
	{
		$_options	 = array(
			'search_regex'	 => false,
			'show_only'		 => false,
		);
		$options	 = Hash::merge($_options, $options);

		if ($options['search_regex']) {
			//$data = preg_replace($searchText, $replaceText, $data);
			preg_match_all($searchText, $data, $matches);
			$matches = array_filter($matches);
			if ($matches) {
				$matchList		 = $matches[0];
				$highlightList	 = array();
				foreach ($matchList as $match) {
					if ($options['show_only']) {
						$highlightList[] = '<strong>' . $replaceText . '</strong>';
					} else {
						$highlightList[] = $replaceText;
					}
				}
				$data = preg_replace((array)$searchText, $highlightList, $data);
			}
		} else {
			if ($options['show_only']) {
				$data = str_replace($searchText, '<strong>' . $replaceText . '</strong>', $data);
			} else {
				$data = str_replace($searchText, $replaceText, $data);
			}
		}

		return $data;
	}

	/**
	 * モデル名.フィールド名の文字列から配列を生成して返す
	 * 
	 * @param string $value
	 * @return array
	 */
	public static function splitName($value = '')
	{
		// 例: $value = Page.name
		$exploded		 = explode('.', $value);
		$searchTarget	 = array(
			'modelName'	 => $exploded[0],
			'field'		 => $exploded[1],
		);
		return $searchTarget;
	}

	/**
	 * 独自の設定ファイルが存在するかチェックする
	 * - /Plugin/TextReplace/Config 内に置いたphpファイルの存在をチェックする
	 * 
	 * @return boolean
	 */
	public static function hasOriginalSetting()
	{
		$path			 = self::getPluginPath() . 'Config' . DS;
		$dir			 = new Folder($path);
		$files			 = $dir->find('.*\.php');
		$excludeFile	 = array('setting.php', 'init.php');
		$settingFiles	 = array();
		foreach ($files as $file) {
			if (!in_array($file, $excludeFile)) {
				$settingFiles[] = $file;
			}
		}
		return $settingFiles;
	}

	/**
	 * TextReplaceプラグインのパスを取得する
	 * 
	 * @return string
	 */
	public static function getPluginPath()
	{
		return App::pluginPath('TextReplace');
	}
	
	/**
	 * フィールド名から設定名称を取得する
	 * 
	 * @param string $fieldName
	 * @return string
	 */
	public static function getSettingNameByFieldName($fieldName)
	{
		$setting = Configure::read('TextReplace.target');
		foreach ($setting as $settingKey => $fieldData) {
			foreach($fieldData['fields'] as $key => $value) {
				if($key === $fieldName) {
					return $settingKey;
				}
			}
		}
		return '';
	}
	
}
