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

class TextReplaceLogsController extends TextReplaceAppController
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
	public $adminTitle = 'テキスト置換ログ';

	/**
	 * [ADMIN] 一覧表示
	 *
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '一覧';
		$this->search	 = 'text_replace_logs_index';
		$this->help		 = 'text_replace_logs_index';

		$default = array('named' => array(
				'num'		 => $this->siteConfigs['admin_list_num'],
				'sortmode'	 => 0)
		);
		$this->setViewConditions($this->modelClass, array('default' => $default));

		$conditions		 = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate	 = array(
			'conditions' => $conditions,
			'fields'	 => array(),
			'order'		 => $this->modelClass . '.id DESC',
			'limit'		 => $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate($this->modelClass));

		// モデルID一覧
		$modelIdList	 = $this->{$this->modelClass}->getControlSource('model_id');
		// モデル名一覧
		$modelNameList	 = $this->{$this->modelClass}->getControlSource('model');
		// 対象フィールド名一覧
		$targetFieldList = $this->{$this->modelClass}->getControlSource('target_field');
		// ユーザー一覧
		$userList		 = $this->User->getUserList();

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
		$default	 = array();
		$this->setViewConditions($this->modelClass, array(
			'default'	 => $default,
			'group'		 => $this->Session->read('Auth.User.id'),
			'action'	 => 'admin_index',
		));
		$conditions	 = $this->_createAdminIndexConditions($this->request->data);
		$datas		 = $this->{$this->modelClass}->find('all', array(
			'conditions' => $conditions,
			'recursive'	 => 0
		));

		$this->set('datas', $datas);
		$this->set('csvFileName', date('YmdHis') . '_' . Inflector::underscore($this->modelClass));
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_view($id = null)
	{
		$this->pageTitle = $this->adminTitle . '確認';
		$this->help		 = 'text_replace_logs_index';
		$this->init();
		$originalData	 = array();

		if (!$id) {
			if (in_array('BcMessage', $this->components, true)) {
				$this->BcMessage->setError('無効な処理です。');
			} else {
				$this->setMessage('無効な処理です。', true);
			}
			$this->redirect(array('action' => 'index'));
		}
		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id	 = $id;
			$data							 = $this->{$this->modelClass}->read();

			if ($data) {
				$modelField		 = array(
					$data[$this->modelClass]['model'] . '.' . $data[$this->modelClass]['target_field'] => $data[$this->modelClass]['model_id']
				);
				$originalData	 = $this->getModelData($modelField);
			}
		}
		// ユーザー一覧
		$userList = $this->User->getUserList();

		$this->crumbs[] = array('name' => $this->adminTitle, 'url' => array('plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'index'));
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
			if (in_array('BcMessage', $this->components, true)) {
				$this->BcMessage->setError('無効な処理です。');
			} else {
				$this->setMessage('無効な処理です。', true);
			}

			$this->redirect(array('action' => 'index'));
		}
		if ($this->{$this->modelClass}->delete($id)) {
			$message = 'NO.' . $id . 'のデータを削除しました。';
			if (in_array('BcMessage', $this->components, true)) {
				$this->BcMessage->setError($message);
			} else {
				$this->setMessage($message, true);
			}

			$this->redirect(array('action' => 'index'));
		} else {
			if (in_array('BcMessage', $this->components, true)) {
				$this->BcMessage->setError('データベース処理中にエラーが発生しました。');
			} else {
				$this->setMessage('データベース処理中にエラーが発生しました。', true);
			}

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
			$this->{$this->modelClass}->saveDbLog($data[$this->modelClass]['id'] . ' を削除しました。');
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
		$conditions		 = array();
		$modelId		 = '';
		$modelName		 = '';
		$targetField	 = '';
		$beforeContents	 = '';
		$afterContents	 = '';
		$created_begin	 = '';
		$created_end	 = '';

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

		if (isset($data[$this->modelClass]['created_begin_date'])) {
			$created_begin = $data[$this->modelClass]['created_begin_date'];
		}
		if (!empty($data[$this->modelClass]['created_begin_time'])) {
			$created_begin .= ' ' . $data[$this->modelClass]['created_begin_time'];
		}

		if (isset($data[$this->modelClass]['created_end_date'])) {
			$created_end = $data[$this->modelClass]['created_end_date'];
		}
		if (!empty($data[$this->modelClass]['created_end_time'])) {
			$created_end .= ' ' . $data[$this->modelClass]['created_end_time'];
		}

		unset($data['_Token']);
		unset($data[$this->modelClass]['model_id']);
		unset($data[$this->modelClass]['model']);
		unset($data[$this->modelClass]['target_field']);
		unset($data[$this->modelClass]['before_contents']);
		unset($data[$this->modelClass]['after_contents']);
		unset($data[$this->modelClass]['after_contents']);
		unset($data[$this->modelClass]['created_begin_date']);
		unset($data[$this->modelClass]['created_begin_time']);
		unset($data[$this->modelClass]['created_end_date']);
		unset($data[$this->modelClass]['created_end_time']);

		// 条件指定のないフィールドを解除
		foreach ($data[$this->modelClass] as $key => $value) {
			if ($value === '') {
				unset($data[$this->modelClass][$key]);
			}
		}

		if (!empty($created_begin)) {
			$conditions[$this->modelClass . '.created >='] = $created_begin;
		}
		if (!empty($created_end)) {
			$conditions[$this->modelClass . '.created <='] = $created_end;
		}

		if ($modelId) {
			$conditions[$this->modelClass . '.model_id'] = $modelId;
		}
		if ($modelName) {
			$conditions[$this->modelClass . '.model'] = $modelName;
		}
		if ($targetField) {
			$conditions[$this->modelClass . '.target_field'] = $targetField;
		}

		if ($data[$this->modelClass]) {
			$conditions = $this->postConditions($data);
		}

		// １つの入力指定から複数フィールド検索指定
		if ($beforeContents) {
			$conditions[] = array(
				$this->modelClass . '.before_contents LIKE' => '%' . $beforeContents . '%'
			);
		}
		if ($afterContents) {
			$conditions[] = array(
				$this->modelClass . '.after_contents LIKE' => '%' . $afterContents . '%'
			);
		}

		return $conditions;
	}

}
