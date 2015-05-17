<?php
/**
 * [Helper] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceHelper extends AppHelper {
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
 * 検索語句を置換後で置換する
 * 
 * @param string $data
 * @return string
 */
	public function getReplaceData($data = '') {
		return preg_replace('/'. $this->searchText .'/', $this->replaceText, $data);
	}
	
}
