<?php

/**
 * [Lib] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceUtil extends Object
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
		foreach ($setting as $model => $fieldData) {
			$useModel[] = $model;
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

		if (ClassRegistry::isKeySet('Plugin')) {
			$PluginModel = ClassRegistry::getObject('Plugin');
		} else {
			$PluginModel = ClassRegistry::init('Plugin');
		}

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
			if ($PluginModel->hasAny($conditions)) {
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
				$exploded	 = explode('.', $key);
				$fieldName[] = $exploded[1];
			}
			$data[$fieldData['name']] = $fieldName;
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
			if ($targetModelName === $fieldData['name']) {
				$fieldName = '';
				foreach ($fieldData['fields'] as $key => $value) {
					$exploded	 = explode('.', $key);
					$fieldName	 = $exploded[1];
					if ($targetFieldName === $fieldName) {
						$fieldTitle = $value;
						break;
					}
				}
			}
		}

		return $fieldTitle;
	}

	/**
	 * 検索語句を置換後で置換する
	 * 
	 * @param string $data
	 * @return string
	 */
	public static function getReplaceData($data = '', $searchText = '', $replaceText = '', $options = array())
	{
		$_options	 = array(
			'search_regex' => false,
		);
		$options	 = Hash::merge($_options, $options);

		$result = '';
		if ($options['search_regex']) {
			$result = preg_replace($searchText, $replaceText, $data);
		} else {
			$result = str_replace($searchText, $replaceText, $data);
		}
		return $result;
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

}
