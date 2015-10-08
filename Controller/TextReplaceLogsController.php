<?php
/**
 * [Controller] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
class TextReplaceLogsController extends BcPluginAppController
{
	/**
	 * ControllerName
	 * 
	 * @var string
	 */
	public $name = 'TextReplaceLogs';

	/**
	 * Model
	 * 
	 * @var array
	 */
	public $uses = array('TextReplace.TextReplaceLog');

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
	public $adminTitle = 'テキスト置換ログ';

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
	 * [ADMIN] 一覧表示
	 * 
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '一覧';
		$this->search = 'text_replace_logs_index';
		$this->help = 'text_replaces_index';

		$default = array('named' => array(
			'num' => $this->siteConfigs['admin_list_num'],
			'sortmode' => 0)
		);
		$this->setViewConditions($this->modelClass, array('default' => $default));

		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate = array(
			'conditions'	=> $conditions,
			'fields'		=> array(),
			'order'	=> $this->modelClass .'.id DESC',
			'limit'			=> $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate($this->modelClass));

		// モデルID一覧
		$modelIdList = $this->{$this->modelClass}->getControlSource('model_id');
		// モデル名一覧
		$modelNameList = $this->{$this->modelClass}->getControlSource('model');
		// 対象フィールド名一覧
		$targetFieldList = $this->{$this->modelClass}->getControlSource('target_field');
		// ユーザー一覧
		$userList = $this->User->getUserList();

		$this->set(compact('modelIdList', 'modelNameList', 'targetFieldList', 'userList'));

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			Configure::write('debug', 0);
			$this->render('ajax_index');
			return;
		}
	}

	/**
	 * [ADMIN] CSVファイルをダウンロードする
	 * 
	 */
	public function admin_download_csv()
	{
		$default = array();
		$this->setViewConditions($this->modelClass, array(
			'default' => $default,
			'group' => $this->Session->read('Auth.User.id'),
			'action' => 'admin_index',
		));
		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$datas = $this->{$this->modelClass}->find('all', array(
			'conditions' => $conditions,
			'recursive' => 0
		));

		$this->set('datas', $datas);
		$this->set('csvFileName', date('YmdHis') .'_'. Inflector::underscore($this->modelClass));
	}

	/**
	 * [ADMIN] 編集
	 * 
	 * @param int $id
	 */
	public function admin_view($id = null)
	{
		$this->pageTitle = $this->adminTitle . '確認';
		$this->help = 'text_replaces_index';
		$this->init();
		$originalData = array();

		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));			
		}
		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id = $id;
			$data = $this->{$this->modelClass}->read();

			if ($data) {
				$modelField = array(
					$data[$this->modelClass]['model'] .'.'. $data[$this->modelClass]['target_field'] => $data[$this->modelClass]['model_id']
				);
				$originalData = $this->getModelData($modelField);
			}
		}
		// ユーザー一覧
		$userList = $this->User->getUserList();

		$this->set(compact('data', 'userList', 'originalData'));
		$this->render('view');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 */
	public function admin_delete($id = null)
	{
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}
		if ($this->{$this->modelClass}->delete($id)) {
			$message = 'NO.' . $id . 'のデータを削除しました。';
			$this->setMessage($message);
			$this->redirect(array('action' => 'index'));
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $id
	 */
	public function admin_ajax_delete($id = null)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		// 削除実行
		if ($this->_delete($id)) {
			clearViewCache();
			exit(true);
		}
		exit();
	}
	
	/**
	 * データを削除する
	 * 
	 * @param int $id
	 * @return boolean 
	 */
	protected function _delete($id)
	{
		// メッセージ用にデータを取得
		$data = $this->{$this->modelClass}->read(null, $id);
		// 削除実行
		if ($this->{$this->modelClass}->delete($id)) {
			$this->{$this->modelClass}->saveDbLog($data[$this->modelClass]['id'] .' を削除しました。');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 一括削除
	 * 
	 * @param array $ids
	 * @return boolean
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach ($ids as $id) {
				$this->_delete($id);
			}
		}
		return true;
	}

	/**
	 * 一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	public function _createAdminIndexConditions($data)
	{
		$conditions = array();
		$modelId = '';
		$modelName = '';
		$targetField = '';
		$beforeContents = '';
		$afterContents = '';

		if (isset($data[$this->modelClass]['model_id'])) {
			$modelId = $data[$this->modelClass]['model_id'];
		}
		if (isset($data[$this->modelClass]['model'])) {
			$modelName = $data[$this->modelClass]['model'];
		}
		if (isset($data[$this->modelClass]['target_field'])) {
			$targetField = $data[$this->modelClass]['target_field'];
		}
		if (isset($data[$this->modelClass]['before_contents'])) {
			$beforeContents = $data[$this->modelClass]['before_contents'];
		}
		if (isset($data[$this->modelClass]['after_contents'])) {
			$afterContents = $data[$this->modelClass]['after_contents'];
		}

		unset($data['_Token']);
		unset($data[$this->modelClass]['model_id']);
		unset($data[$this->modelClass]['model']);
		unset($data[$this->modelClass]['target_field']);
		unset($data[$this->modelClass]['before_contents']);
		unset($data[$this->modelClass]['after_contents']);

		// 条件指定のないフィールドを解除
		foreach($data[$this->modelClass] as $key => $value) {
			if ($value === '') {
				unset($data[$this->modelClass][$key]);
			}
		}

		if ($modelId) {
			$conditions[$this->modelClass .'.model_id'] = $modelId;
		}
		if ($modelName) {
			$conditions[$this->modelClass .'.model'] = $modelName;
		}
		if ($targetField) {
			$conditions[$this->modelClass .'.target_field'] = $targetField;
		}

		if ($data[$this->modelClass]) {
			$conditions = $this->postConditions($data);
		}

		// １つの入力指定から複数フィールド検索指定
		if ($beforeContents) {
			$conditions[] = array(
				$this->modelClass .'.before_contents LIKE' => '%'.$beforeContents.'%'
			);
		}
		if ($afterContents) {
			$conditions[] = array(
				$this->modelClass .'.after_contents LIKE' => '%'.$afterContents.'%'
			);
		}

		return $conditions;
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
