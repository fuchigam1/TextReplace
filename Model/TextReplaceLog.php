<?php
/**
 * [Model] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
class TextReplaceLog extends AppModel
{

	/**
	 * ModelName
	 *
	 * @var string
	 */
	public $name = 'TextReplaceLog';

	/**
	 * PluginName
	 *
	 * @var string
	 */
	public $plugin = 'TextReplace';

	/**
	 * Behavior
	 *
	 * @var array
	 */
	public $actsAs = array(
		'BcCache',
	);

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @param array $options
	 * @return mixed $controlSource コントロールソース
	 */
	public function getControlSource($field, $options = array())
	{
		$controlSources = array();

		switch ($field) {
			case 'model_id':
				$conditions	 = array(
					'fields' => array('DISTINCT ' . $this->name . '.' . $field)
				);
				$allModelId	 = $this->find('all', $conditions);
				if ($allModelId) {
					foreach ($allModelId as $modelId) {
						$controlSources[$modelId[$this->name][$field]] = $modelId[$this->name][$field];
					}
				}
				break;

			case 'model':
				$conditions	 = array(
					'fields' => array('DISTINCT ' . $this->name . '.' . $field)
				);
				$allModelId	 = $this->find('all', $conditions);
				if ($allModelId) {
					foreach ($allModelId as $modelId) {
						$controlSources[$modelId[$this->name][$field]] = $modelId[$this->name][$field];
					}
				}
				break;
			case 'target_field':
				$conditions	 = array(
					'fields' => array('DISTINCT ' . $this->name . '.' . $field)
				);
				$allModelId	 = $this->find('all', $conditions);
				if ($allModelId) {
					foreach ($allModelId as $modelId) {
						$controlSources[$modelId[$this->name][$field]] = $modelId[$this->name][$field];
					}
				}
				break;
		}

		if ($controlSources) {
			return $controlSources;
		} else {
			return false;
		}
	}

}
