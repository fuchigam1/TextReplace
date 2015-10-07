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
	private $hasFieldError = false;
	
	/**
	 * 設定ファイルのフィールド指定に誤りがある場合のメッセージ
	 * 
	 * @var string
	 */
	private $errorFieldInfo = '';
	
	/**
	 * 設定ファイルの設定値
	 * 
	 * @var array
	 */
	protected $pluginSetting = array();
	
	/**
	 * beforeFilter
	 * 
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->pluginSetting = Configure::read('TextReplace');
	}
	
	/**
	 * 初期化処理
	 * - 設定不備をチェックする
	 * 
	 */
	private function init()
	{
		// 設定ファイルのモデル指定から、利用可能なモデルと不可のモデルを設定する
		TextReplaceUtil::init($this->pluginSetting['target']);
		$isEnableSearch = true;		// 検索実行可能判定
		
		$disabledModelList = TextReplaceUtil::getDisabledModel();
		if ($disabledModelList) {
			$disabledModel = implode('、', $disabledModelList);
			$this->setMessage('設定ファイルに利用できないモデルの指定があります。<br>（'. $disabledModel .'）', true);
			$isEnableSearch = false;
		}
		
		$useModel = TextReplaceUtil::getEnabledModel();
		$this->uses = Hash::merge($this->uses, $useModel);
		
		// 設定ファイルのフィールド指定にエラーがないかチェックする
		$this->hasFieldError = $this->hasFieldError($this->pluginSetting['target']);
		if ($this->hasFieldError) {
			$this->setMessage($this->errorFieldInfo, true);
			$isEnableSearch = false;
		}
		
		$this->set('isEnableSearch', $isEnableSearch);
	}
	
	/**
	 * [ADMIN] 検索、置換確認
	 * 
	 */
	public function admin_index()
	{
		$this->help = 'text_replaces_index';
		$this->pageTitle = 'テキスト置換処理';
		$this->init();

		$user = $this->BcAuth->user();
		$datas = array();	// 検索結果一覧のデータ
		$searchText = '';
		$replaceText = '';
		$searchTarget = array();
		$searchType = '';
		$message = '';
		
		if ($this->request->data) {
			if (!$this->isNoinputSearchReplace($this->request->data)) {
				
				$searchText = $this->request->data['TextReplace']['search_pattern'];	// 検索語句
				$replaceText = $this->request->data['TextReplace']['replace_pattern'];	// 置換後
				$useRegex = $this->request->data['TextReplace']['search_regex'];		// 正規表現の利用指定
				$searchType = $this->request->data['TextReplace']['type'];				// 検索タイプ
				$countResult = 0;	// 検索結果数
				
				// 実行ボタン別に処理を行う
				switch ($searchType) {
					case 'search-and-replace':
						if (!empty($this->request->data['ReplaceTarget'])) {
							$hasPageSaveResult = false;		// 固定ページのデータ置換の有無
							clearAllCache();
							foreach ($this->request->data['ReplaceTarget'] as $resultKey => $value) {
								$target = $this->getTargetModelField($value);
								$targetModel = $target['modelName'];
								$targetField = $target['field'];
								
								$originalData = $this->getModelData($value);
								if ($originalData) {
									$data = $this->getReplaceData($originalData, $searchText, $replaceText,
											array(
												'search_regex' => $useRegex,
												'target_model' => $targetModel,
												'target_field' => $targetField,
											)
									);

									//$saveResult = true;
									$saveResult = $this->{$targetModel}->save($data, array('callbacks' => false, 'validate' => false));
									if ($saveResult) {
										$this->saveLogging(array(
											'original' => $originalData,
											'save_result' => $saveResult,
											'search_pattern' => $searchText,
											'replace_pattern' => $replaceText,
											'search_regex' => $useRegex,
											'model' => $targetModel,
											'target_field' => $targetField,
											'user_id' => $user['id'],
										));
										$datas[$targetModel][$targetField][] = $originalData;
										$countResult++;
										if ($targetModel === 'Page') {
											$hasPageSaveResult = true;
										}
									}
								}
							}
							$message = '検索置換を実行しました。';
							if ($hasPageSaveResult) {
								$message .= '「固定ページテンプレート書出」を実行してください。';
							}
							$this->setMessage($message);
						} else {
							$message = '置換対象が選択されていません。';
						}
						break;

					case 'search':
					case 'dryrun':
					default:
						clearAllCache();
						foreach ($this->request->data['TextReplace']['replace_target'] as $value) {
							$searchTarget = TextReplaceUtil::splitName($value);
							$targetModel = $searchTarget['modelName'];
							$targetField = $searchTarget['field'];
							
							// 検索置換対象指定から、モデル別に検索語句を含むデータを全て取得する
							$allData = $this->getSearchResult($targetModel, $targetField, $searchText,
									array('use_regex' => $useRegex)
							);

							if ($allData) {
								if (!$useRegex) {
									// 正規表現検索を利用しない場合、単純に検索結果データに入れ込む
									$datas[$targetModel][$targetField] = $allData;
									$countResult = $countResult + count($allData);
								} else {
									$result = array();
									foreach ($allData as $resultKey => $resultValue) {
										// 正規表現検索を利用する場合、検索にヒットしたデータの文字列内に、パターン(検索語句)にマッチするデータがあるか判定する
										// ヒットした場合: データ内の、パターン(検索語句)にマッチする文字列をコールバック関数を利用して書き換える
										if (preg_match($searchText, $resultValue[$targetModel][$targetField])) {
											// preg_replace_callback 関数は正規表現にマッチした文字列を コールバック関数 replaceHitString に配列で渡す
											$allData[$resultKey][$targetModel][$targetField] = preg_replace_callback(
													$searchText,
													array($this, 'replaceHitString'),
													$resultValue[$targetModel][$targetField]
											);
											$result[] = $allData[$resultKey];
										}
									}
									if ($result) {
										$datas[$targetModel][$targetField] = $result;
										$countResult = $countResult + count($result);
									}
								}
							}
						}
						$message = '該当する検索語句がありませんでした。';
						break;
				}

				if (!$countResult) {
					$this->setMessage($message, true);
				}
			}
		} else {
			$this->setDefaultSearchTarget();
		}
		$query = $this->getSearchReplaceText($searchType, array('search_text' => $searchText, 'replace_text' => $replaceText));
		
		// 検索置換対象の指定内容を作成
		$replaceTarget = TextReplaceUtil::getReplaceTarget($this->pluginSetting['target']);
		
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
	 * 検索文字列、置換後文字列を取得する
	 * 
	 * @param string $searchType
	 * @param array $options
	 * @return array
	 */
	private function getSearchReplaceText($searchType, $options = array())
	{
		$_options = array(
			'search_text' => '',
			'replace_text' => '',
		);
		$options = Hash::merge($_options, $options);
		
		$query[] = $options['search_text'];
		if ($options['replace_text']) {
			if ($searchType == 'dryrun') {
				$query[] = $options['replace_text'];
			}
		}
		return $query;
	}
	
	/**
	 * 検索置換対象の指定 の初期値を設定する
	 * 
	 */
	private function setDefaultSearchTarget()
	{
		$defaultTarget = $this->pluginSetting['default_replace_target'];
		if ($defaultTarget) {
			$this->request->data['TextReplace']['replace_target'] = $defaultTarget;
		}
	}
	
	/**
	 * 検索・置換対象のモデル名とフィールド名と取得する
	 * 
	 * @param array $modelField array(Model.field => id値)
	 * @return array
	 */
	private function getTargetModelField($modelField)
	{
		$valueKey = key($modelField);
		$searchTarget = TextReplaceUtil::splitName($valueKey);
		return $searchTarget;
	}
	
	/**
	 * モデル名.フィールド名 と id値 からデータを取得する
	 * 
	 * @param array $modelField array(Model.field => id値)
	 */
	private function getModelData($modelField)
	{
		$valueKey = key($modelField);
		$searchTarget = $this->getTargetModelField($modelField);
		$targetModel = $searchTarget['modelName'];
		$targetField = $searchTarget['field'];
		
		$originalData = $this->{$targetModel}->find('first', array(
			'conditions' => array($targetModel .'.id' => $modelField[$valueKey]),
			'recursive' => -1,
		));
		
		return $originalData;
	}
	
	/**
	 * 実行結果をログファイルに保存する
	 * 
	 * @param array $options
	 */
	private function saveLogging($options = array())
	{
		$_options = array(
			'original' => array(),
			'save_result' => array(),
		);
		$options = Hash::merge($_options, $options);
		
		// save したデータのログを取る
		if ($options['original']) {
			//$this->log($options['original'], LOG_TEXT_REPLACE_BEFORE);
		}
		if ($options['save_result']) {
			//$this->log($options['save_result'], LOG_TEXT_REPLACE);
		}

		$modelId = $options['original'][$options['model']]['id'];

		if (ClassRegistry::isKeySet('TextReplace.TextReplaceLog')) {
			$TextReplceLogModel = ClassRegistry::getObject('TextReplace.TextReplaceLog');
		} else {
			$TextReplceLogModel = ClassRegistry::init('TextReplace.TextReplaceLog');
		}

		$saveData = array(
			'TextReplaceLog' => array(
				'search_pattern'	 => $options['search_pattern'],
				'replace_pattern'	 => $options['replace_pattern'],
				'search_regex'		 => $options['search_regex'],
				'model_id'			 => $modelId,
				'model'				 => $options['model'],
				'target_field'		 => $options['target_field'],
				'before_contents'	 => $options['original'][$options['model']][$options['target_field']],
				'after_contents'	 => $options['save_result'][$options['model']][$options['target_field']],
				'user_id'			 => $options['user_id'],
			)
		);
		$TextReplceLogModel->create($saveData);
		$result = $TextReplceLogModel->save($saveData, array('callbacks' => false));
		if (!$result) {
			$this->log($saveData, LOG_DEBUG);
		}
	}
	
	/**
	 * 検索語句に指定があるかチェックする
	 * 
	 * @param array $data
	 * @return boolean
	 */
	private function isNoinputSearchReplace($data)
	{
		if (!$data['TextReplace']['search_pattern']) {
			$this->setMessage('検索語句を指定してください。', true);
			return true;
		}
		if (!$data['TextReplace']['replace_target']) {
			$this->setMessage('検索置換対象を指定してください。', true);
			return true;
		}
		return false;
	}
	
	/**
	 * 検索置換対象指定から、検索語句を含むデータを全て取得する
	 * 
	 * @param string $modelName
	 * @param string $field
	 * @param string $searchText
	 * @param array $options
	 * @return array or boolean
	 */
	protected function getSearchResult($modelName, $field, $searchText, $options = array())
	{
		$_options = array(
			'use_regex' => false,
		);
		$options = Hash::merge($_options, $options);
		
		$conditions = array();
		// 'conditions' => array($value .' REGEXP' => "^$param$|^$param,"),
		// $conditions = array($value .' REGEXP' => "$searchText");
		if (!$options['use_regex']) {
			$target = implode('.', array($modelName, $field));
			$conditions = array($target .' LIKE' => "%{$searchText}%");
		}

		$allData = $this->{$modelName}->find('all', array(
			'conditions' => $conditions,
			'order' => '',
			'recursive' => -1,
		));
		return $allData;
	}
	
	/**
	 * 元データ内の検索語句を置換指定語句で変換した内容を取得する
	 * 
	 * @param array $originalData
	 * @param string $searchText
	 * @param string $replaceText
	 * @param array $options
	 * @return array
	 */
	protected function getReplaceData($originalData, $searchText = '', $replaceText = '', $options = array())
	{
		$_options = array(
			'search_regex' => false,
			'target_model' => '',
			'target_field' => '',
		);
		$options = Hash::merge($_options, $options);
		
		$useRegex = $options['search_regex'];
		$targetModel = $options['target_model'];
		$targetField = $options['target_field'];
		
		$saveData = $originalData;
		$saveData[$targetModel][$targetField] = TextReplaceUtil::getReplaceData(
				$originalData[$targetModel][$targetField],
				$searchText, $replaceText, array('search_regex' => $useRegex));
		
		return $saveData;
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
	protected function hasFieldError($setting = array())
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
