<?php
/**
 * [View] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
$this->BcBaser->css('TextReplace.admin/text_replace', array('inline' => false));
?>
<table cellpadding="0" cellspacing="0" class="form-table" id="ListTable">
	<tr>
		<th>NO</th>
		<td><?php echo $data['TextReplaceLog']['id'] ?></td>
		<th>実施日時（実施ユーザー）</th>
		<td>
			<?php echo $this->BcTime->format('Y/m/d H:i:s', $data['TextReplaceLog']['created']) ?>
			（<?php echo $userList[$data['TextReplaceLog']['user_id']] ?>）
		</td>
	</tr>
	<tr>
		<th>検索置換時の対象モデル名</th>
		<td><?php echo $data['TextReplaceLog']['model'] ?></td>
		<th>検索置換時の対象フィールド名</th>
		<td>
			<?php echo $data['TextReplaceLog']['target_field'] ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php $editLink = $this->TextReplace->getEditUrl($data['TextReplaceLog']['model'], $originalData) ?>
			<?php if ($editLink): ?>
				<?php echo $this->BcBaser->getLink('≫ 編集画面', $editLink) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>検索語句
			<small>
			&nbsp;&nbsp;&nbsp;&nbsp;正規表現の利用
			（<?php echo $this->BcText->booleanMark($data['TextReplaceLog']['search_regex']); ?>）
			</small>
		</th>
		<td colspan="3">
			<?php echo $data['TextReplaceLog']['search_pattern'] ?>
		</td>
	</tr>
	<tr>
		<th>置換後</th>
		<td colspan="3">
			<?php echo $data['TextReplaceLog']['replace_pattern'] ?>
		</td>
	</tr>
</table>

<div class="box-field-result">
<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th>検索置換前の内容</th>
		<th>検索置換後の内容</th>
	</tr>
	<tr>
		<td class="replace-before">
			<?php echo $this->TextReplace->getBeforeSearchReplaceData(
					$data['TextReplaceLog']['before_contents'],
					$data['TextReplaceLog']['search_pattern'],
					$data['TextReplaceLog']['search_regex']) ?>
		</td>
		<td class="replace-after">
			<?php echo $this->BcBaser->mark($data['TextReplaceLog']['replace_pattern'], nl2br(h($data['TextReplaceLog']['after_contents']))) ?>
		</td>
	</tr>
</table>
</div>

<?php if (BcUtil::isAdminUser()): ?>
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
<?php endif ?>
