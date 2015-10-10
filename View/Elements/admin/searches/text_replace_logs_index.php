<?php
/**
 * [Controller] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
?>
<?php echo $this->BcForm->create('TextReplaceLog', array('url' => array('action' => 'index'))) ?>
<p>
	<span>
	<?php echo $this->BcForm->label('TextReplaceLog.created', '実施日時') ?></span>
	<?php echo $this->BcForm->dateTimePicker('TextReplaceLog.created_begin', array('size' => 12)) ?>
	〜
	<?php echo $this->BcForm->dateTimePicker('TextReplaceLog.created_end', array('size' => 12)) ?>
	</span>
</p>
<p>
	<span>
		<?php echo $this->BcForm->label('TextReplaceLog.model_id', 'モデルID') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.model_id', array('type' => 'select', 'options' => $modelIdList, 'empty' => '指定なし')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span>
		<?php echo $this->BcForm->label('TextReplaceLog.model', 'モデル名') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.model', array('type' => 'select', 'options' => $modelNameList, 'empty' => '指定なし')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span>
		<?php echo $this->BcForm->label('TextReplaceLog.target_field', '対象フィールド名') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.target_field', array('type' => 'select', 'options' => $targetFieldList, 'empty' => '指定なし')) ?>
	</span>
</p>
<p>
	<span>
		<?php echo $this->BcForm->label('TextReplaceLog.before_contents', '検索置換前の内容') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.before_contents', array('type' => 'text', 'size' => '30')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span>
		<?php echo $this->BcForm->label('TextReplaceLog.after_contents', '検索置換後の内容') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.after_contents', array('type' => 'text', 'size' => '30')) ?>
	</span>
</p>
<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $this->BcForm->end() ?>
