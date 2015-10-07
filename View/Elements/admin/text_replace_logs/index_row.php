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
<tr>
	<td class="row-tools">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->checkbox('ListTool.batch_targets.' . $data['TextReplaceLog']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['TextReplaceLog']['id'])) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_view.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), array('action' => 'view', $data['TextReplaceLog']['id']), array('title' => '確認')) ?>
		<?php if (BcUtil::isAdminUser()): ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')),
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
