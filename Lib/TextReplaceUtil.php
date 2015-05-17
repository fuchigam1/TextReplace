<?php
/**
 * [Lib] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceUtil extends Object {
	
/**
 * getUseModel
 * 設定ファイルから利用モデルを取得する
 * 
 * @param array $setting
 * @return array
 */
	public static function getUseModel($setting = array()) {
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
	public static function getReplaceTarget($setting = array()) {
		$replaceTarget = array();
		foreach ($setting as $model => $fieldData) {
			$keyName = $setting[$model]['title'] .'（'. $model. '）';
			$replaceTarget[$keyName] = $fieldData['fields'];
		}
		return $replaceTarget;
	}
	
/**
 * getUseModelName
 * 設定ファイルから利用モデル名を取得する
 * 
 * @param array $setting
 * @return array
 */
	public static function getUseModelName($setting = array()) {
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
	public static function getModelField($setting = array()) {
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
	
}
