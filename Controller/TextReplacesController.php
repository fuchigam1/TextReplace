<?php
/**
 * [Controller] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplacesController extends BcPluginAppController
{
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
//		'TextReplace',
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
	 * 設定ファイルの設定値
	 * 
	 * @var array
	 */
	public $pluginSetting = array();
	
	/**
	 * beforeFilter
	 * 
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->pluginSetting = Configure::read('TextReplace.target');
	}
	
	/**
	 * [ADMIN] 検索、置換確認
	 * 
	 */
	public function admin_index()
	{
		$this->help = 'text_replaces_index';
		$this->pageTitle = 'テキスト置換処理';
		
		$setting = $this->pluginSetting;
		$useModel = TextReplaceUtil::getUseModel($setting);
		$this->uses = Hash::merge($this->uses, $useModel);
		
		// 検索置換対象の指定内容を作成
		$replaceTarget = TextReplaceUtil::getReplaceTarget($setting);
		
		// 設定ファイルのフィールド指定にエラーがないかチェックする
		$this->judgeFieldError = $this->judgeFieldError($setting);
		if ($this->judgeFieldError) {
			$this->setMessage($this->errorFieldInfo, true);
		}
		
		$datas = array();	// 検索結果一覧のデータ
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
			$useRegex = $this->request->data['TextReplace']['search_regex'];		// 正規表現の利用指定
			$searchType = $this->request->data['TextReplace']['type'];				// 検索タイプ
			$countResult = 0;	// 検索結果数
			
			// 実行ボタン別に処理を行う
			switch ($this->request->data['TextReplace']['type']) {
				case 'search-and-replace':
					$this->search_and_replace($this->request->data, $searchText, $replaceText);
					break;
				case 'search':
				case 'dryrun':
				default:
					foreach ($this->request->data['TextReplace']['replace_target'] as $value) {
						$exploded = explode('.', $value);
						$searchTarget = array(
							'modelName' => $exploded[0],
							'field' => $exploded[1],
						);
						
						$conditions = array();
						// 'conditions' => array($value .' REGEXP' => "^$param$|^$param,"),
						// $conditions = array($value .' REGEXP' => "$searchText");
						if (!$useRegex) {
							$conditions = array($value .' LIKE' => "%{$searchText}%");
						}
						
						$allData = $this->{$searchTarget['modelName']}->find('all', array(
							'fields' => array('id', $searchTarget['field']),
							'conditions' => $conditions,
							'order' => '',
							'recursive' => -1,
						));
						
						if ($allData) {
							if (!$useRegex) {
								$datas[$searchTarget['modelName']][$searchTarget['field']] = $allData;
								$countResult = $countResult + count($allData);
							} else {
								$result = array();
								if ($allData) {
									foreach ($allData as $resultKey => $resultValue) {
										if (preg_match($searchText, $resultValue[$searchTarget['modelName']][$searchTarget['field']])) {
											// preg_replace_callback 関数は正規表現にマッチした文字列を コールバック関数 replaceHitString に配列で渡す
											$allData[$resultKey][$searchTarget['modelName']][$searchTarget['field']] = preg_replace_callback(
													$searchText,
													array($this, 'replaceHitString'),
													$resultValue[$searchTarget['modelName']][$searchTarget['field']]
											);
											$result[] = $allData[$resultKey];
										}
//										if (preg_match($searchText, $resultValue[$searchTarget['modelName']][$searchTarget['field']])) {
//											$result[] = $allData[$resultKey];
//										}
									}
								}
								if ($result) {
									$datas[$searchTarget['modelName']][$searchTarget['field']] = $result;
									$countResult = $countResult + count($result);
								}
							}
						}
					}
					break;
			}
			if (!$countResult) {
				$this->setMessage('該当する検索語句がありませんでした。');
			}
		}
		
		$query = array();
		if ($replaceText) {
			$query = array($searchText, $replaceText);
		}
		
		$this->set(compact('query', 'searchText', 'replaceText', 'replaceTarget', 'searchType', 'countResult'));
		$this->set('datas', $datas);
	}
	
	/**
	 * callback: preg_replace_callback
	 * 
	 * @param array $matches
	 * @return string
	 */
	private function replaceHitString($matches)
	{
		foreach ($matches as $key => $value) {
			$matches[$key] = str_replace($value, ''. $value .'', $value);
		}
		return $matches[$key];
	}
	
	/**
	 * [ADMIN] 検索、置換確認
	 * 
	 */
	protected function search_and_replace($data, $searchText = '', $replaceText = '')
	{
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
	protected function createSearchConditions($data)
	{
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
	protected function judgeFieldError($setting = array())
	{
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
