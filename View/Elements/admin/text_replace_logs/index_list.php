<?php
/**
 * [Controller] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
$this->BcListTable->setColumnNumber(8);
?>
<?php if ($siteConfig['admin_theme']): ?>
<div class="bca-data-list__top">
	<!-- 一括処理 -->
	<?php if ($this->BcBaser->isAdminUser()): ?>
		<div class="bca-action-table-listup">
				<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
				<?php echo $this->BcForm->button(
					__d('baser', '適用'),
					['id' => 'BtnApplyBatch', 'disabled' => 'disabled', 'class' => 'bca-btn', 'data-bca-btn-size' => 'lg']
				) ?>
		</div>
	<?php endif ?>
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
	</div>
</div>


<table class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
		<tr>
			<th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser', '一括選択') ?>">
				<?php if ($this->BcBaser->isAdminUser()): ?>
					<?php echo $this->BcForm->input('ListTool.checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser', '一括選択')]) ?>
				<?php endif; ?>
			</th>
			<th class="bca-table-listup__thead-th">
				<?php echo $this->Paginator->sort('id',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No')
					],
					['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
				) ?>
			</th>
			<th class="bca-table-listup__thead-th">
				<?php echo $this->Paginator->sort('model_id',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'モデルID'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'モデルID')
					],
					['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
				) ?>
			</th>
			<th class="bca-table-listup__thead-th">
				<?php echo $this->Paginator->sort('model',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'モデル名'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'モデル名')
					],
					['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
				) ?>
				<br />
				<?php echo $this->Paginator->sort('target_field',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '対象フィールド名'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '対象フィールド名')
					],
					['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
				) ?>
			</th>
			<th class="bca-table-listup__thead-th">検索置換前の内容</th>
			<th class="bca-table-listup__thead-th">検索置換後の内容</th>
			<th class="bca-table-listup__thead-th">
				<?php echo $this->Paginator->sort('created',
					[
						'asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '実施日時'),
						'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '実施日時')
					],
					['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
				) ?>
			</th>
			<th class="bca-table-listup__thead-th"><?php // アクション ?>
				<?php echo __d('baser', 'アクション') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($datas)): ?>
			<?php foreach($datas as $data): ?>
				<?php $this->BcBaser->element('text_replace_logs/index_row', array('data' => $data)) ?>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td">
					<p class="no-data"><?php echo __d('baser', 'データがありません。') ?></p>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<div class="bca-data-list__bottom">
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
		<!-- list-num -->
		<?php $this->BcBaser->element('list_num') ?>
	</div>
</div>

<?php return; ?>
<?php endif; ?>
<? // admin-secondは以下 ?>


<?php $this->BcBaser->element('pagination') ?>

<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool">
			<?php if ($this->BcBaser->isAdminUser()): ?>
			<div>
				<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
				<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
				<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
			</div>
			<?php endif ?>
			</th>
			<th><?php echo $this->Paginator->sort('id', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' NO',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' NO'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('model_id', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' モデルID',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' モデルID'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th><?php echo $this->Paginator->sort('model', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' モデル名',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' モデル名'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
				<br />
				<?php echo $this->Paginator->sort('target_field', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 対象フィールド名',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 対象フィールド名'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
			<th>検索置換前の内容</th>
			<th>検索置換後の内容</th>
			<th><?php echo $this->Paginator->sort('created', array(
					'asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')).' 実施日時',
					'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')).' 実施日時'),
					array('escape' => false, 'class' => 'btn-direction')) ?>
			</th>
		</tr>
	</thead>
	<tbody>
<?php if(!empty($datas)): ?>
	<?php foreach($datas as $data): ?>
		<?php $this->BcBaser->element('text_replace_logs/index_row', array('data' => $data)) ?>
	<?php endforeach; ?>
<?php else: ?>
		<tr>
			<td colspan="7"><p class="no-data">データがありません。</p></td>
		</tr>
<?php endif; ?>
	</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
