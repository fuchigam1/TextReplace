<?php
/**
 * [View] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
$this->BcBaser->css('TextReplace.admin/text_replace', array('inline' => false));
?>
<table cellpadding="0" cellspacing="0" class="list-table bca-form-table" id="ListTable">
	<tr>
		<th class="col-head bca-form-table__label">NO</th>
		<td class="col-input bca-form-table__input"><?php echo $data['TextReplaceLog']['id'] ?></td>
		<th class="col-head bca-form-table__label">実施日時（実施ユーザー）</th>
		<td class="col-input bca-form-table__input">
			<?php echo $this->BcTime->format('Y/m/d H:i:s', $data['TextReplaceLog']['created']) ?>
			（<?php echo $userList[$data['TextReplaceLog']['user_id']] ?>）
		</td>
	</tr>
	<tr>
		<th class="col-head bca-form-table__label">検索置換時の対象モデル名</th>
		<td class="col-input bca-form-table__input"><?php echo $data['TextReplaceLog']['model'] ?></td>
		<th class="col-head bca-form-table__label">検索置換時の対象フィールド名</th>
		<td class="col-input bca-form-table__input">
			<?php echo $data['TextReplaceLog']['target_field'] ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php $editLink = $this->TextReplace->getEditUrl($data['TextReplaceLog']['model'], $originalData) ?>
			<?php if ($editLink): ?>
				<?php echo $this->BcBaser->getLink('≫ 編集画面', $editLink) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th class="col-head bca-form-table__label">検索語句
			<small>
			&nbsp;&nbsp;&nbsp;&nbsp;正規表現の利用
			（<?php echo $this->BcText->booleanMark($data['TextReplaceLog']['search_regex']); ?>）
			</small>
		</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo h($data['TextReplaceLog']['search_pattern']) ?>
		</td>
	</tr>
	<tr>
		<th class="col-head bca-form-table__label">置換後</th>
		<td class="col-input bca-form-table__input" colspan="3">
			<?php echo h($data['TextReplaceLog']['replace_pattern']) ?>
		</td>
	</tr>
	<tr>
		<th class="col-head bca-form-table__label">置換確認用URL
			<?php if (!$siteConfig['admin_theme']): ?>
				<?php echo $this->BcBaser->getImg('admin/icn_help.png', array('id' => 'helpTextReplaceLogQueryUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextTextReplaceLogQueryUrl" class="helptext">
					置換＆保存実行時の条件で置換確認を再現できます。
				</div>
			<?php endif; ?>
		</th>
		<td class="col-input bca-form-table__input" colspan="3" style="word-break: break-all; word-wrap: break-word;">
			<?php
				if ($data['TextReplaceLog']['query_url']) {
					$data['TextReplaceLog']['query_url'] = trim($data['TextReplaceLog']['query_url']);
				}
			?>
			<small>
				<?php $this->BcBaser->link($data['TextReplaceLog']['query_url'], $data['TextReplaceLog']['query_url']) ?>
			</small>
			<?php if ($siteConfig['admin_theme']): ?>
				<br>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<div class="helptext">
					置換＆保存実行時の条件で置換確認を再現できます。
				</div>
			<?php endif; ?>
		</td>
	</tr>
</table>

<div class="box-field-result">
<table cellpadding="0" cellspacing="0" class="list-table bca-form-table">
	<tr>
		<th class="col-head bca-form-table__label">検索置換前の内容</th>
		<th class="col-head bca-form-table__label">検索置換後の内容</th>
	</tr>
	<tr>
		<td class="col-input replace-before">
			<?php echo nl2br(
				TextReplaceUtil::getBeforeSearchReplaceData(
					h($data['TextReplaceLog']['before_contents']),
					h($data['TextReplaceLog']['search_pattern']),
					h($data['TextReplaceLog']['search_pattern']),
					array('search_regex' => $data['TextReplaceLog']['search_regex'])
				)
			); ?>
		</td>
		<td class="col-input replace-after">
			<?php echo nl2br(
				TextReplaceUtil::getReplaceData(
					h($data['TextReplaceLog']['after_contents']),
					h($data['TextReplaceLog']['replace_pattern']),
					h($data['TextReplaceLog']['replace_pattern']),
					array('show_only' => true)
				)
			); ?>
		</td>
	</tr>
</table>
</div>


<?php if ($siteConfig['admin_theme']): ?>
	<?php if (BcUtil::isAdminUser()): ?>
	<!-- button -->
	<div class="bca-actions">
		<?php
		$this->BcBaser->link(__d('baser', '削除'),
			[
				'action' => 'delete', $data['TextReplaceLog']['id']
			],
			[
				'id' => 'BtnDeleteTextReplaceLogs',
				'class' => 'submit-token button bca-btn bca-actions__item btn-gray',
				'data-bca-btn-type' => 'delete',
				'data-bca-btn-size' => 'sm',
				'data-bca-btn-color' => 'danger',
				'data-bca-btn-type' => 'delete'
			],
			sprintf(__d('baser', 'ログ NO「%s」を削除します。よろしいですか？'), $data['TextReplaceLog']['id']),
			false
		);
		?>
	</div>
	<?php endif; ?>
<?php else: ?>
	<?php // admin-second用 ?>
	<div class="submit">
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $data['TextReplaceLog']['id']),
			array('class' => 'btn-gray button', 'id' => 'BtnDeleteTextReplaceLogs')); ?>
	</div>
	<script>
	$(function(){
		/**
		 * 削除ボタン押下時
		 */
		$("#BtnDeleteTextReplaceLogs").click(function() {
			$.bcConfirm.show({
				'title': '検索置換ログ削除',
				'message':'<p><strong><?php echo sprintf('ログ NO「%s」を削除します。', $data['TextReplaceLog']['id']) ?></strong></p>' +
							'<p>よろしいですか？</p>',
				'ok':function(){
					location.href = $("#BtnDeleteTextReplaceLogs").attr('href');
				}
			});
			return false;
		});
	});
	</script>
<?php endif; ?>
