<?php
/**
 * [Controller] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
App::uses('TextReplaceAppController', 'TextReplace.Controller');

class TextReplacesController extends TextReplaceAppController
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
	public $uses = array('TextReplace.TextReplace');

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
		array('name' => 'テキスト置換管理', 'url' => array('plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'))
	);

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'テキスト置換';

	/**
	 * [ADMIN] 検索、置換確認
	 *
	 * - 検索置換実行時は post 送信とする（414 Request-URI too large が発生する点を考慮）
	 */
	public function admin_index()
	{
		$this->help		 = 'text_replaces_index';
		$this->pageTitle = 'テキスト置換処理';
		$this->init();

		$user							 = $this->BcAuth->user();
		$datas							 = array(); // 検索結果一覧のデータ
		$searchText						 = '';
		$replaceText					 = '';
		$searchTarget					 = array();
		$searchType						 = '';
		$message						 = '';
		$linkContainingQueryParameter	 = ''; // 検索クエリーを含むURL
		// 検索、置換確認時
		if ($this->request->is('get')) {
			if ($this->request->query) {
				$this->request->data['TextReplace'] = $this->request->query;
				if (!empty($this->request->query['data'])) {
					$this->request->data['ReplaceTarget'] = $this->request->query['data']['ReplaceTarget'];
				}
				$linkContainingQueryParameter = $this->getLinkContainingQueryParameter($this->request->query);
			}
		}

		// 検索置換実行時: View側で、検索置換実行時、jQuery で form::method を post に切替えている
		if ($this->request->is('post')) {
			$this->isEnableSearchAndReplace = true;

			if (isset($this->request->data['ReplaceTarget'])) {
				$replaceTargetData						 = $this->request->data['ReplaceTarget'];
				unset($this->request->data['ReplaceTarget']);
				$requestQuery							 = $this->request->data;
				$this->request->data['ReplaceTarget']	 = $replaceTargetData;
				unset($replaceTargetData);
			}
			$this->request->data['TextReplace'] = $this->request->data;

			// 検索置換実行記録のタイプを dryrun として変換し、置換ログデータに dryrun として記録し、リンクから呼び出せるようにする
			$requestQuery['type']			 = 'dryrun';
			$linkContainingQueryParameter	 = $this->getLinkContainingQueryParameter($requestQuery);
		}

		$countResult = 0; // 検索結果数
		if ($this->request->data) {
			clearAllCache();

			$this->TextReplace->set($this->request->data);
			$errors = array();
			if (!$this->TextReplace->validates()) {
				$errors = $this->TextReplace->validationErrors;
				$message .= '入力エラーです。内容を修正してください。';
			}

			if (!$this->isNoinputSearchReplace($this->request->data) && !$errors) {

				$searchText	 = $this->request->data['TextReplace']['search_pattern']; // 検索語句
				$replaceText = $this->request->data['TextReplace']['replace_pattern']; // 置換後
				$useRegex	 = $this->request->data['TextReplace']['search_regex'];  // 正規表現の利用指定
				$searchType	 = $this->request->data['TextReplace']['type'];	// 検索タイプ
				// 実行ボタン別に処理を行う
				switch ($searchType) {
					case 'search-and-replace':
						if ($this->isEnableSearchAndReplace) {
							if (!empty($this->request->data['ReplaceTarget'])) {
								$hasPageSaveResult = false;  // 固定ページのデータ置換の有無
								foreach ($this->request->data['ReplaceTarget'] as $resultKey => $value) {
									$target		 = $this->getTargetModelField($value);
									$targetModel = $target['modelName'];
									$targetField = $target['field'];
									clearDataCache();
									$originalData = $this->getModelData($value);
									if ($originalData) {
										$data = $this->getReplaceData($originalData, $searchText, $replaceText, array(
											'search_regex'	 => $useRegex,
											'target_model'	 => $targetModel,
											'target_field'	 => $targetField,
												)
										);

										//$saveResult = true;
										$options = ['callbacks' => false, 'validate' => false];
										if($targetModel === 'Content') {
											// Content モデルの場合、URLが変更となる可能性があるので callbacks が必要
											unset($options['callbacks']);
										}
										$saveResult = $this->{$targetModel}->save($data, $options);
										if ($saveResult) {
											$this->saveLogging(array(
												'original'			 => $originalData,
												'save_result'		 => $saveResult,
												'search_pattern'	 => $searchText,
												'replace_pattern'	 => $replaceText,
												'search_regex'		 => $useRegex,
												'model'				 => $targetModel,
												'target_field'		 => $targetField,
												'user_id'			 => $user['id'],
												'query_url'			 => $linkContainingQueryParameter,
											));
											$datas[$targetModel][$targetField][] = $originalData;
											$countResult++;
											$settingName = TextReplaceUtil::getSettingNameByFieldName(key($value));
											if ($settingName === 'Page') {
												$hasPageSaveResult = true;
											}
										}
									}
								}
								$message = '検索置換を実行しました。[' . $countResult . '件]';
								if ($hasPageSaveResult) {
									$message .= '　「固定ページテンプレート書出」を実行してください。';
								}
								$this->setMessage($message, false, true);
							} else {
								$message = '置換対象が選択されていません。';
							}
						} else {
							$message = '置換＆保存はURLアクセスからの実行はできません。置換確認実行後に利用してください。';
						}
						break;

					case 'search':
					case 'dryrun':
					default:
						// 正規表現検索時、バックスラッシュで検索語句を指定していない場合のErrorをキャッチするための ErrorHandler
						// http://stackoverflow.com/questions/30005616/can-missing-delimiter-errors-in-a-preg-php-regexp-be-read-programmatically
						set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
							// error was suppressed with the @-operator
							if (0 === error_reporting()) {
								return false;
							}
							throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
						});
						$hasSearchReplaceError = false;

						foreach ($this->request->data['TextReplace']['replace_target'] as $value) {
							$searchTarget	 = TextReplaceUtil::splitName($value);
							$targetModel	 = $searchTarget['modelName'];
							$targetField	 = $searchTarget['field'];

							// 検索置換対象指定から、モデル別に検索語句を含むデータを全て取得する
							$allData = $this->getSearchResult($targetModel, $targetField, $searchText, array('use_regex' => $useRegex));

							if ($allData) {
								if (!$useRegex) {
									// 正規表現検索を利用しない場合、単純に検索結果データに入れ込む
									$datas[$targetModel][$targetField]	 = $allData;
									$countResult						 = $countResult + count($allData);
								} else {
									// 正規表現検索時、正規表現にエラーがある場合やメモリオーバー時はエラーとする
									if (!empty($allData['error'])) {
										$hasSearchReplaceError = true;
										$message .= $allData['message'];
									} else {
										$result = array();
										foreach ($allData as $resultKey => $resultValue) {
											// 正規表現検索を利用する場合、検索にヒットしたデータの文字列内に、パターン(検索語句)にマッチするデータがあるか判定する
											// ヒットした場合: データ内の、パターン(検索語句)にマッチする文字列をコールバック関数を利用して書き換える
											try {
												if (preg_match($searchText, $resultValue[$targetModel][$targetField])) {
													// preg_replace_callback 関数は正規表現にマッチした文字列を コールバック関数 replaceHitString に配列で渡す
													$allData[$resultKey][$targetModel][$targetField] = preg_replace_callback(
														$searchText, array($this, 'replaceHitString'), $resultValue[$targetModel][$targetField]
													);

													$result[] = $allData[$resultKey];
												}
											} catch (Exception $exc) {
												$message .= $exc->getMessage();
												$hasSearchReplaceError = true;
												break;
											}
										}

										if ($result) {
											$datas[$targetModel][$targetField]	 = $result;
											$countResult						 = $countResult + count($result);
										}
									}
								}
							}
							if ($hasSearchReplaceError) {
								break;
							}
						}
						$message .= '該当する検索語句がありませんでした。';
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

		$this->set(compact('query', 'searchText', 'replaceText', 'replaceTarget', 'searchType', 'countResult', 'linkContainingQueryParameter'));
		$this->set('datas', $datas);
	}

	/**
	 * 検索、置換確認時時のURLを取得する
	 *
	 * @param array $requestQuery
	 * @return string
	 */
	private function getLinkContainingQueryParameter($requestQuery)
	{
		$baseUrl = array(
			'admin'		 => $this->request->params['admin'],
			'plugin'	 => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action'	 => $this->request->params['action'],
		);

		return Router::url(Hash::merge($baseUrl, array('?' => $requestQuery)), true);
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
			$matches[$key] = str_replace($value, '' . $value . '', $value);
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
		$_options	 = array(
			'search_text'	 => '',
			'replace_text'	 => '',
		);
		$options	 = Hash::merge($_options, $options);

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
	 * 実行結果をログファイルに保存する
	 *
	 * @param array $options
	 */
	private function saveLogging($options = array())
	{
		$_options	 = array(
			'original'		 => array(),
			'save_result'	 => array(),
		);
		$options	 = Hash::merge($_options, $options);

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

		$saveData	 = array(
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
				'query_url'			 => trim($options['query_url']),
			)
		);
		$TextReplceLogModel->create($saveData);
		$result		 = $TextReplceLogModel->save($saveData, array('callbacks' => false));
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
		$_options	 = array(
			'use_regex' => false,
		);
		$options	 = Hash::merge($_options, $options);

		$conditions = array();
		// 'conditions' => array($value .' REGEXP' => "^$param$|^$param,"),
		if ($options['use_regex']) {
			$allData = $this->getRegexSearchResult($modelName, $field, $searchText);
		} else {
			$target		 = implode('.', array($modelName, $field));
			$conditions	 = array($target . ' LIKE' => "%{$searchText}%");

			if ($modelName === 'Content') {
				$conditions['NOT'] = $this->TextReplace->getConditionTrash();
			}

			$allData = $this->{$modelName}->find('all', array(
				'conditions' => $conditions,
				'order'		 => '',
				'recursive'	 => -1,
			));
		}

		return $allData;
	}

	/**
	 * 正規表現で検索した場合の検索結果一覧を取得する
	 * - DB直で検索できないため、対象モデルのデータ一覧を取得後、1レコードずつ検索する
	 * - 正規表現指定時のエラーの場合、エラーメッセージを返す
	 *
	 * @param string $modelName 対象モデル名
	 * @param string $field 対象フィールド名
	 * @param string $searchText 検索語句
	 * @return array 検索結果一覧
	 */
	private function getRegexSearchResult($modelName, $field, $searchText)
	{
		$matchDataList = array();

		try {
			$allData = $this->{$modelName}->find('all', array(
				'conditions' => array(),
				'order'		 => '',
				'recursive'	 => -1,
			));

			if ($allData) {
				foreach ($allData as $key => $data) {
					preg_match_all($searchText, $data[$modelName][$field], $matches);
					$filteredMatch = array_filter($matches);
					if ($filteredMatch) {
						$matchDataList[$key] = $data;
					} else {
						unset($allData[$key]);
					}
				}
				unset($allData);
			}
		} catch (Exception $exc) {
			$message = $exc->getMessage();
			$this->log($message, LOG_TEXT_REPLACE);
			$this->log($exc->getTraceAsString(), LOG_TEXT_REPLACE);
			$this->log("NOW UseMemory: " . memory_get_usage() / (1024 * 1024) . "MB", LOG_TEXT_REPLACE);

			$error = array(
				'error'		 => true,
				'message'	 => $message,
			);
			return $error;
		}

		return $matchDataList;
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
		$_options	 = array(
			'search_regex'	 => false,
			'target_model'	 => '',
			'target_field'	 => '',
		);
		$options	 = Hash::merge($_options, $options);

		$useRegex	 = $options['search_regex'];
		$targetModel = $options['target_model'];
		$targetField = $options['target_field'];

		$saveData								 = $originalData;
		$saveData[$targetModel][$targetField]	 = TextReplaceUtil::getReplaceData(
						$originalData[$targetModel][$targetField], $searchText, $replaceText, array('search_regex' => $useRegex));

		return $saveData;
	}

}
