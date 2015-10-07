<?php
/**
 * [View] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
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
		<td><?php echo $data['TextReplaceLog']['target_field'] ?></td>
	</tr>
	<tr>
		<th>検索語句
			<small>
			&nbsp;&nbsp;検索置換時の正規表現利用
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

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th>検索置換前の内容</th>
		<th>検索置換後の内容</th>
	</tr>
	<tr>
		<td><?php echo nl2br(h($data['TextReplaceLog']['before_contents'])) ?></td>
		<td><?php echo nl2br(h($data['TextReplaceLog']['after_contents'])) ?></td>
	</tr>
</table>

<?php if (BcUtil::isAdminUser()): ?>
<div class="submit">
	<?php $this->BcBaser->link('削除', array('action' => 'delete', $data['TextReplaceLog']['id']), array('class' => 'btn-gray button'),
		sprintf('ログ NO「%s」を削除してもいいですか？', $data['TextReplaceLog']['id']), false); ?>
</div>
<?php endif ?>
