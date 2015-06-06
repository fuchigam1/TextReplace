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
		return $useModel;
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
			$keyName = $setting[$model]['title'] .'（'. $model. '）';
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
	public static function getUseModelName($setting = array())
	{
		$useModel = array();
		foreach ($setting as $model => $fieldData) {
			$useModel[] = $fieldData['name'];
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
				$exploded = explode('.', $key);
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
		$setting = Configure::read('TextReplace.target');
		$fieldTitle = '';
		
		foreach ($setting as $model => $fieldData) {
			if ($targetModelName === $model) {
				$fieldName = '';
				foreach ($fieldData['fields'] as $key => $value) {
					$exploded = explode('.', $key);
					$fieldName = $exploded[1];
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
		$_options = array(
			'search_regex' => false,
		);
		$options = Hash::merge($_options, $options);
		
		$result = '';
		if ($options['search_regex']) {
			$result = preg_replace($searchText, $replaceText, $data);
		} else {
			$result = str_replace($searchText, $replaceText, $data);
		}
		return $result;
	}
	
}
