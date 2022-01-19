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
<?php if ($siteConfig['admin_theme']): ?>
<tr>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select"><?php // 選択 ?>
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['TextReplaceLog']['id'], array('type' => 'checkbox', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['TextReplaceLog']['id'], 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>')) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->BcBaser->link($data['TextReplaceLog']['id'], array('action' => 'view', $data['TextReplaceLog']['id']), array('title' => '確認')) ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $data['TextReplaceLog']['model_id'] ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $data['TextReplaceLog']['model'] ?><br />
		<?php echo $data['TextReplaceLog']['target_field'] ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->Text->truncate(h($data['TextReplaceLog']['before_contents']), 70) ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo $this->Text->truncate(h($data['TextReplaceLog']['after_contents']), 70) ?>
	</td>
	<td class="bca-table-listup__tbody-td" style="white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['TextReplaceLog']['created']) ?>
		<br />
		<?php echo $this->BcTime->format('H:i:s', $data['TextReplaceLog']['created']) ?>
	</td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
		<?php $this->BcBaser->link('', ['action' => 'view', $data['TextReplaceLog']['id']], ['title' => __d('baser', '確認'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
		<?php if (BcUtil::isAdminUser()): ?>
			<?php $this->BcBaser->link('', ['action' => 'ajax_delete', $data['TextReplaceLog']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		<?php endif ?>
	</td>
</tr>
<?php return; ?>
<?php endif; ?>
<? // admin-secondは以下 ?>

<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['TextReplaceLog']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['TextReplaceLog']['id'])) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('alt' => '確認', 'class' => 'btn')), array('action' => 'view', $data['TextReplaceLog']['id']), array('title' => '確認')) ?>
		<?php if (BcUtil::isAdminUser()): ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')),
			array('action' => 'ajax_delete', $data['TextReplaceLog']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
		<?php endif ?>
		</td>
	<td>
		<?php echo $this->BcBaser->link($data['TextReplaceLog']['id'], array('action' => 'view', $data['TextReplaceLog']['id']), array('title' => '確認')) ?>
	</td>
	<td><?php echo $data['TextReplaceLog']['model_id'] ?></td>
	<td>
		<?php echo $data['TextReplaceLog']['model'] ?><br />
		<?php echo $data['TextReplaceLog']['target_field'] ?>
	</td>
	<td><?php echo $this->Text->truncate(h($data['TextReplaceLog']['before_contents']), 70) ?></td>
	<td><?php echo $this->Text->truncate(h($data['TextReplaceLog']['after_contents']), 70) ?></td>
	<td style="white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['TextReplaceLog']['created']) ?>
		<br />
		<?php echo $this->BcTime->format('H:i:s', $data['TextReplaceLog']['created']) ?>
	</td>
</tr>
