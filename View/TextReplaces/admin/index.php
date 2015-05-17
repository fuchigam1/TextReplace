<?php
/**
 * [ADMIN] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
$this->TextReplace->searchText = $searchText;
$this->TextReplace->replaceText = $replaceText;

$rowspan = '';
$judgeReplace = false;
if ($searchType === 'replace') {
	$rowspan = ' rowspan="2"';
	$judgeReplace = true;
}
?>
<script type="text/javascript">
	$(window).load(function() {
		$("#TextReplaceSearchPattern").focus();
	});

	$(function () {
		// 検索ボタン 実行時
		$('#BtnTypeSearch').on('click', function(){
			$('#TextReplaceType').val('search');
//			if (!confirm('検索置換対象の指定内容で、検索語句を一括検索します。\n宜しいですか？')) {
//				return false;
//			}
		});

		// 置換確認ボタン 実行時
		$('#BtnTypeReplace').on('click', function(){
			$('#TextReplaceType').val('replace');
//			if (!confirm('検索置換対象の指定内容で、検索語句を一括検索し、置換後の状態を表示します。\n宜しいですか？')) {
//				return false;
//			}
		});

		// 検索＆保存ボタン 実行時
		$('#BtnTypeSearchAndReplace').on('click', function(){
			$('#TextReplaceType').val('search-and-replace');
			if (!confirm('実行前に必ずDBのバックアップを取ってください。\n検索語句を置換後の内容で一括変換します。\n宜しいですか？')) {
				return false;
			}
		});

		// 検索置換対象のチェックボックスを全てチェックする
		$('#TextReplaceCheckAll').on('click', function(){
			if ($(this).prop('checked')) {
				$('.target-check input[type=checkbox]').prop('checked', true);
			} else {
				$('.target-check input[type=checkbox]').prop('checked', false);
			}
		});
//		$('#TextReplaceCheckAll').children('fieldset legend').on('click', function(){
//			$('#TextReplaceCheckAll').children('fieldset').children('.checkbox input[type=checkbox]').each();
//		});

		// 検索結果一覧の見方を表示する
		$('#TextReplaceInsight').hide();
		$('#helpTextReplaceInsight').on('click', function(){
			$('#TextReplaceInsight').slideToggle();
		});

		// モデル別の検索結果を開閉する
		$('.box-model-result h3').on('click', function(){
			console.log($(this).next());
			$(this).next().slideToggle();
		});

		// 置換対象指定チェックボックスを全てチェックする
		if ($('#TextReplaceCheckBoxModelResult').prop('checked')) {
			$('.box-model-result input[type=checkbox]').prop('checked', true);
		}
		$('#TextReplaceCheckBoxModelResult').on('click', function(){
			if ($(this).prop('checked')) {
				$('.box-model-result input[type=checkbox]').prop('checked', true);
			} else {
				$('.box-model-result input[type=checkbox]').prop('checked', false);
			}
		});

		// モデル別の検索結果数を表示する
		$('.box-field-result-all').each(function(){
			var count = $(this).find('.field-count').html();
			$(this).parent().children('h3').append(count + '件');
		});

	});
</script>

<style type="text/css">
	.target-check fieldset legend {
		font-weight: bold;
	}
	.target-check .checkbox {
		margin-right: 10px;
		margin-bottom: 6px;
	}
</style>

<?php echo $this->BcForm->create('TextReplace', array('url' => array('action' => 'index'))) ?>
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tbody>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.search_pattern', '検索語句') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('TextReplace.search_pattern', array('type' => 'text', 'size' => '76')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpSearchPattern', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextSearchPattern" class="helptext">
					<ul>
						<li>検索する文字列を指定します。</li>
						<li>文字列には正規表現を利用できます。「/〜/」は不要です。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('TextReplace.search') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.replace_pattern', '置換後') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('TextReplace.replace_pattern', array('type' => 'text', 'size' => '76')) ?>
				<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => 'helpReplacePattern', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextReplacePattern" class="helptext">
					<ul>
						<li>置換後の文字列を指定します。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('TextReplace.replace_pattern') ?>
			</td>
		</tr>
	</tbody>
</table>

<table cellpadding="0" cellspacing="0" class="form-table section">
	<tbody>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.replace_target', '検索置換対象の指定') ?></th>
			<td class="col-input target-check">
				<?php echo $this->BcForm->input('TextReplace.replace_target', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $replaceTarget)); ?>
				<?php echo $this->BcForm->error('TextReplace.replace_target') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.check_all', '全て選択') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('TextReplace.check_all', array('type' => 'checkbox', 'label' => '検索置換対象全てにチェックを入れる')); ?>
				<?php echo $this->BcForm->error('TextReplace.check_all') ?>
			</td>
		</tr>
	</tbody>
</table>


<?php echo $this->BcForm->input('TextReplace.type', array('type' => 'hidden', 'value' => '')) ?>
<div class="submit">
	<?php $confirmMessageSearch = '検索置換対象の指定内容で、検索語句を一括検索します。\n宜しいですか？' ?>
	<?php //echo $this->BcForm->submit('検索', array('div' => false, 'id' => 'BtnTypeSearch', 'class' => 'button', 'onClick'=>"return confirm('$confirmMessageSearch')")) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'id' => 'BtnTypeSearch', 'class' => 'button')) ?>

	<?php $confirmMessageReplace = '検索置換対象の指定内容で、検索語句を一括検索し、置換後の状態を表示します。\n宜しいですか？' ?>
	<?php //echo $this->BcForm->submit('置換確認', array('div' => false, 'id' => 'BtnTypeReplace', 'class' => 'button', 'onClick'=>"return confirm('$confirmMessageReplace')")) ?>
	<?php echo $this->BcForm->submit('置換確認', array('div' => false, 'id' => 'BtnTypeReplace', 'class' => 'button')) ?>
</div>
<?php //echo $this->BcForm->end() ?>



<?php if ($datas): ?>
<?php //echo $this->BcForm->create('TextReplace', array('url' => array('action' => 'batch_replace'))) ?>

<h2 id="helpTextReplaceInsight">実行結果一覧（<?php echo $countResult; ?>件）
	&nbsp;<?php echo $this->BcBaser->img('admin/icn_help.png', array('id' => '', 'class' => 'btn', 'alt' => 'ヘルプ')) ?>
</h2>

<div id="TextReplaceInsight">
	<h3>コンテンツ（モデル名）・・・クリックすると結果を開閉できます。</h3>
	<table cellpadding="0" cellspacing="0" class="list-table form-table">
		<tbody>
				<tr>
					<th class="col-head"<?php echo $rowspan; ?>>
						フィールド名（ID:モデルID）
					</th>
					<?php if ($judgeReplace): ?>
					<td class="col-input" nowrap="nowrap"<?php echo $rowspan; ?>>
						置換対象<br />チェック
					</td>
					<?php endif ?>
					<td class="col-input" style="width: 100%;">
						検索結果
					</td>
				</tr>
				<?php if ($rowspan): ?>
				<tr>
					<td class="col-input">
						検索置換結果
					</td>
				</tr>
				<?php endif ?>
		</tbody>
	</table>
</div>

<?php if ($judgeReplace): ?>
<table cellpadding="0" cellspacing="0" class="form-table section">
	<tbody>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.check_box_model_result', '全て選択') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('TextReplace.check_box_model_result', array('type' => 'checkbox', 'label' => '置換対象全てにチェックを入れる')); ?>
				<?php echo $this->BcForm->error('TextReplace.check_box_model_result') ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif ?>


<?php foreach ($datas as $modelName => $modelData): ?>
<div class="box-model-result">
<h3><?php echo $this->BcText->arrayValue($modelName, $this->TextReplace->getModelList()) ?>（<?php echo $modelName; ?>）</h3>
	<?php $fieldCount = 0; ?>
	<div class="box-field-result-all">
	<?php foreach ($modelData as $fieldName => $fieldValue): ?>
		<?php $fieldCount = $fieldCount + count($fieldValue); ?>
		<div class="box-field-result">
		<table cellpadding="0" cellspacing="0" class="list-table form-table">
			<tbody>
				<?php foreach ($fieldValue as $num => $result): ?>
				<tr>
					<th class="col-head"<?php echo $rowspan; ?>>
						<?php echo $fieldName; ?>（ID:<?php echo $result[$modelName]['id']; ?>）
					</th>
					<?php if ($judgeReplace): ?>
					<td class="col-input" nowrap="nowrap"<?php echo $rowspan; ?>>
						<label for="TextReplaceTarget<?php echo $modelName . $result[$modelName]['id']; ?>">
							<input type="checkbox" name="data[<?php echo $modelName; ?>][<?php echo $fieldName; ?>][]" value="<?php echo $result[$modelName]['id']; ?>" id="TextReplaceTarget<?php echo $modelName . $result[$modelName]['id']; ?>">
						</label>
					</td>
					<?php endif ?>
					<td class="col-input" style="width: 100%;">
						<?php echo $this->BcBaser->mark($query, h($result[$modelName][$fieldName])) ?>
					</td>
				</tr>
				<?php if ($rowspan): ?>
				<tr>
					<td class="col-input">
						<?php echo $this->BcBaser->mark($query, h($this->TextReplace->getReplaceData($result[$modelName][$fieldName]))) ?>
					</td>
				</tr>
				<?php endif ?>
				<?php endforeach ?>
			</tbody>
		</table>
		</div>
	<?php endforeach ?>
		<span class="field-count display-none"><?php echo $fieldCount; ?></span>
	</div>
</div>
<?php endforeach ?>

<div class="submit">
	<?php $confirmMessageSearchAndReplace = '実行前に必ずDBのバックアップを取ってください。\n検索語句を置換後の内容で一括変換します。\n宜しいですか？'; ?>
	<?php //echo $this->BcForm->submit('置換＆保存', array('div' => false, 'id' => 'BtnTypeSearchAndReplace', 'class' => 'button', 'onClick'=>"return confirm('$confirmMessageSearchAndReplace')")) ?>
	<?php echo $this->BcForm->submit('置換＆保存', array('div' => false, 'id' => 'BtnTypeSearchAndReplace', 'class' => 'button')) ?>
</div>
<?php endif ?>


<?php echo $this->BcForm->end() ?>
