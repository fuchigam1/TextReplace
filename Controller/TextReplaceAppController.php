<?php
/**
 * [Controller] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
class TextReplaceAppController extends AppController
{

	/**
	 * 設定ファイルのフィールド指定の誤り判定
	 *
	 * @var boolean
	 */
	protected $hasFieldError = false;

	/**
	 * 設定ファイルのフィールド指定に誤りがある場合のメッセージ
	 *
	 * @var string
	 */
	protected $errorFieldInfo = '';

	/**
	 * 置換＆保存が可能かどうかの判定値
	 *
	 * @var boolean
	 */
	protected $isEnableSearchAndReplace = false;

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
	protected function init()
	{
		ini_set('memory_limit', '126M');
		ini_set('max_execution_time', 180);
		set_time_limit(180);

		// 設定ファイルのモデル指定から、利用可能なモデルと不可のモデルを設定する
		TextReplaceUtil::init($this->pluginSetting['target']);
		$isEnableSearch = true;  // 検索実行可能判定

		$disabledModelList = TextReplaceUtil::getDisabledModel();
		if ($disabledModelList) {
			$disabledModel	 = implode('、', $disabledModelList);
			$this->setMessage('設定ファイルに利用できないモデルの指定があります。<br>（' . $disabledModel . '）', true);
			$isEnableSearch	 = false;
		}

		$useModel	 = TextReplaceUtil::getEnabledModel();
		$this->uses	 = Hash::merge($this->uses, $useModel);

		// 設定ファイルのフィールド指定にエラーがないかチェックする
		$this->hasFieldError = $this->hasFieldError($this->pluginSetting['target']);
		if ($this->hasFieldError) {
			$this->setMessage($this->errorFieldInfo, true);
			$isEnableSearch = false;
		}

		$this->set('isEnableSearch', $isEnableSearch);
	}

	/**
	 * 検索・置換対象のモデル名とフィールド名と取得する
	 *
	 * @param array $modelField array(Model.field => id値)
	 * @return array
	 */
	protected function getTargetModelField($modelField)
	{
		$valueKey		 = key($modelField);
		$searchTarget	 = TextReplaceUtil::splitName($valueKey);
		return $searchTarget;
	}

	/**
	 * モデル名.フィールド名 と id値 からデータを取得する
	 *
	 * @param array $modelField array(Model.field => id値)
	 */
	protected function getModelData($modelField)
	{
		$valueKey		 = key($modelField);
		$searchTarget	 = $this->getTargetModelField($modelField);
		$targetModel	 = $searchTarget['modelName'];
		$targetField	 = $searchTarget['field'];

		$originalData = $this->{$targetModel}->find('first', array(
			'conditions' => array($targetModel . '.id' => $modelField[$valueKey]),
			'recursive'	 => -1
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
		$useModelName	 = TextReplaceUtil::getUseModelName($setting);
		// 検索置換対象となるモデルとフィールドの一覧を取得
		$modelAndField	 = TextReplaceUtil::getModelField($setting);
		foreach ($useModelName as $value) {
			$modelFields = $this->{$value}->getColumnTypes();
			//var_dump($useModelName);
			foreach ($modelAndField as $key => $field) {
				if ($value === $key) {
					foreach ($field as $check) {
						if (!array_key_exists($check, $modelFields)) {
							$error					 = true;
							$this->errorFieldInfo	 = $value . 'モデル内に' . $check . 'フィールドは存在しません。TextReplaceの設定ファイルを修正してください。';
							break;
						}
					}
				}
			}
		}
		return $error;
	}

}
