<?php
/**
 * [Controller] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
?>
<?php echo $this->BcForm->create('TextReplaceLog', array('url' => array('action' => 'index'))) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php if ($siteConfig['admin_theme']): ?>
			<span class="bca-datetimepicker__group">
				<span class="bca-datetimepicker__date">
					<label for="TextReplaceLogCreatedBegin" class="bca-datetimepicker__date-label">実施開始日付</label>
					<?php echo $this->BcForm->input('TextReplaceLog.created_begin', [
						'type' => 'datePicker',
						'size' => 12,
						'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '実施開始日付')],
						'timeLabel' => ['text' => __d('baser', '実施開始時間')],
						'div' => false,
					], true) ?>
				</span>
				<span class="bca-datetimepicker__delimiter">〜</span>
				<span class="bca-datetimepicker__date">
					<label for="TextReplaceLogCreatedEnd" class="bca-datetimepicker__date-label">実施終了日付</label>
					<?php echo $this->BcForm->input('TextReplaceLog.created_end', [
						'type' => 'datePicker',
						'size' => 12,
						'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '実施終了日付')],
						'timeLabel' => ['text' => __d('baser', '実施終了時間')],
						'div' => false,
					], true) ?>
				</span>
			</span>
		<?php else: ?>
			<?php echo $this->BcForm->label('TextReplaceLog.created', '実施日時') ?></span>
			<?php echo $this->BcForm->dateTimePicker('TextReplaceLog.created_begin', array('size' => 12)) ?>
			〜
			<?php echo $this->BcForm->dateTimePicker('TextReplaceLog.created_end', array('size' => 12)) ?>
		<?php endif; ?>
	</span>
</p>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('TextReplaceLog.model_id', 'モデルID') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.model_id', array('type' => 'select', 'options' => $modelIdList, 'empty' => '指定なし')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('TextReplaceLog.model', 'モデル名') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.model', array('type' => 'select', 'options' => $modelNameList, 'empty' => '指定なし')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('TextReplaceLog.target_field', '対象フィールド名') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.target_field', array('type' => 'select', 'options' => $targetFieldList, 'empty' => '指定なし')) ?>
	</span>
</p>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('TextReplaceLog.before_contents', '検索置換前の内容') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.before_contents', array('type' => 'text', 'size' => '30')) ?>
	</span>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('TextReplaceLog.after_contents', '検索置換後の内容') ?>
		&nbsp;<?php echo $this->BcForm->input('TextReplaceLog.after_contents', array('type' => 'text', 'size' => '30')) ?>
	</span>
</p>

<?php if ($siteConfig['admin_theme']): ?>
<div class="button bca-search__btns">
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-btn-lg', 'data-bca-btn-size' => "lg"]) ?></div>
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn']) ?></div>
</div>
<?php else: ?>
<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?>
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?>
</div>
<?php endif; ?>
<?php echo $this->BcForm->end() ?>
