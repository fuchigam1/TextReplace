<?php
/**
 * [Controller] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplacesController extends BcPluginAppController {
/**
 * ControllerName
 * 
 * @var string
 */
	public $name = 'TextReplaces';
	
/**
 * Model
 * 
 * @var array
 */
	public $uses = array(
//		'Page',
//		'Blog.BlogPost'
	);
	
/**
 * Helpers
 * 
 * @var array
 */
	public $helpers = array('BcForm');
	
/**
 * Components
 * 
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');
	
/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = array('text_replace');
	
/**
 * ぱんくずナビ
 *
 * @var string
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'テキスト置換プラグイン', 'url' => array('plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'))
	);
	
/**
 * 管理画面タイトル
 *
 * @var string
 */
	public $adminTitle = 'テキスト置換';
	
/**
 * 設定ファイルのフィールド指定の誤り判定
 * 
 * @var boolean
 */
	public $judgeFieldError = false;
	
/**
 * 設定ファイルのフィールド指定に誤りがある場合のメッセージ
 * 
 * @var string
 */
	public $errorFieldInfo = '';
	
/**
 * [ADMIN] 検索、置換確認
 * 
 */
	public function admin_index() {
		$this->help = 'text_replaces_index';
		$this->pageTitle = 'テキスト置換処理';
		
		$setting = Configure::read('TextReplace.target');
		$useModel = TextReplaceUtil::getUseModel($setting);
		$this->uses = Hash::merge($this->uses, $useModel);
		
		// 検索置換対象の指定内容を作成
		$replaceTarget = TextReplaceUtil::getReplaceTarget($setting);
		
		// 設定ファイルのフィールド指定にエラーがないかチェックする
		$this->judgeFieldError = $this->judgeFieldError($setting);
		if ($this->judgeFieldError) {
			$this->setMessage($this->errorFieldInfo, true);
		}
		
		$datas = array();
		$searchText = '';
		$replaceText = '';
		$searchTarget = array();
		$searchType = '';
		
		if ($this->request->data) {
			if (!$this->request->data['TextReplace']['search_pattern']) {
				$this->setMessage('検索語句を指定してください。', true);
				$this->redirect(array('action' => 'index'));
			}
			if (!$this->request->data['TextReplace']['replace_target']) {
				$this->setMessage('検索置換対象を指定してください。', true);
				$this->redirect(array('action' => 'index'));
			}
			
			$searchText = $this->request->data['TextReplace']['search_pattern'];	// 検索語句
			$replaceText = $this->request->data['TextReplace']['replace_pattern'];	// 置換後
			$searchType = $this->request->data['TextReplace']['type'];
			
			// 実行ボタン別に処理を行う
			switch ($this->request->data['TextReplace']['type']) {
				case 'search':
				case 'replace':
				case 'search-and-replace':
					$this->search_and_replace($this->request->data, $searchText, $replaceText);
				default:
					foreach ($this->request->data['TextReplace']['replace_target'] as $value) {
						$exploded = explode('.', $value);
						$searchTarget = array(
							'modelName' => $exploded[0],
							'field' => $exploded[1],
						);
						// $conditions = array($value .' LIKE' => "%{$searchText}%");
						// 'conditions' => array($value .' REGEXP' => "^$param$|^$param,"),
						$conditions = array($value .' REGEXP' => "$searchText");
						// $conditions = $this->createSearchConditions($this->request->data);
						if ($conditions) {
							$result = $this->{$searchTarget['modelName']}->find('all', array(
								'fields' => array('id', $searchTarget['field']),
								'conditions' => $conditions,
								'order' => '',
								'recursive' => -1,
							));
							if ($result) {
								// 検索語句を置換後の文字列で置換する処理
//								foreach ($result as $num => $resultData) {
//									$result[$num][$searchTarget['modelName']][$searchTarget['field']]
//									= preg_replace('/'. $searchText .'/', $replaceText, $result[$num][$searchTarget['modelName']][$searchTarget['field']]);
//								}
								$datas[$searchTarget['modelName']][$searchTarget['field']] = $result;
							}
						}
					}
					break;
			}
			
		}
		
		$query = array();
		if ($replaceText) {
			$query = array($searchText, $replaceText);
		}
		
		$this->set(compact('query', 'searchText', 'replaceText', 'replaceTarget', 'searchType'));
		$this->set('datas', $datas);
	}
	
/**
 * [ADMIN] 検索、置換確認
 * 
 */
	protected function search_and_replace($data, $searchText = '', $replaceText = '') {
		
		foreach ($data['TextReplace']['replace_target'] as $value) {
			$exploded = explode('.', $value);
			$searchTarget = array(
				'modelName' => $exploded[0],
				'field' => $exploded[1],
			);
			// $conditions = array($value .' LIKE' => "%{$searchText}%");
			// 'conditions' => array($value .' REGEXP' => "^$param$|^$param,"),
			$conditions = array($value .' REGEXP' => "$searchText");
			// $conditions = $this->createSearchConditions($data);
			if ($conditions) {
				$result = $this->{$searchTarget['modelName']}->find('all', array(
					'fields' => array('id', $searchTarget['field']),
					'conditions' => $conditions,
					'order' => '',
					'recursive' => -1,
				));
				if ($result) {
					// 検索語句を置換後の文字列で置換する処理
//					foreach ($result as $num => $resultData) {
//						$result[$num][$searchTarget['modelName']][$searchTarget['field']]
//						= preg_replace('/'. $searchText .'/', $replaceText, $result[$num][$searchTarget['modelName']][$searchTarget['field']]);
//					}
					$datas[$searchTarget['modelName']][$searchTarget['field']] = $result;
				}
			}
		}
		
	}
	
/**
 * 検索条件を生成する
 *
 * @param array $data
 * @return array $conditions
 */
	protected function createSearchConditions($data) {
		$conditions = array();
		$searchText = '';
		
		if ($data['TextReplace']['search_pattern']) {
			$searchText = $data['TextReplace']['search_pattern'];
		}
		
		if ($data['TextReplace']['replace_target']) {
			foreach ($data['TextReplace']['replace_target'] as $key => $value) {
				$conditions[] = array($value .' LIKE' => "%{$searchText}%");
			}
		}
		
		return $conditions;
	}
	
/**
 * 設定ファイルのフィールド指定にエラーがないかチェックする
 * 
 * @param array $setting
 * @return boolean
 */
	protected function judgeFieldError($setting = array()) {
		$error = false;
		
		// 実際に利用するモデル名を取得
		$useModelName = TextReplaceUtil::getUseModelName($setting);
		// 検索置換対象となるモデルとフィールドの一覧を取得
		$modelAndField = TextReplaceUtil::getModelField($setting);
		foreach ($useModelName as $value) {
			$modelFields = $this->{$value}->getColumnTypes();
			//var_dump($useModelName);
			foreach ($modelAndField as $key => $field) {
				if ($value === $key) {
					foreach ($field as $check) {
						if (!array_key_exists($check, $modelFields)) {
							$error = true;
							$this->errorFieldInfo = $value .'モデル内に'. $check .'フィールドは存在しません。TextReplaceの設定ファイルを修正してください。';
							break;
						}
					}
				}
			}
		}
		return $error;
	}
	
}
