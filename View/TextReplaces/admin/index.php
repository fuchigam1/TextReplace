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
$isReplace = false;
if ($searchType === 'dryrun') {
	$rowspan = ' rowspan="2"';
	$isReplace = true;
}

$isSearchAndReplace = false;
if ($searchType === 'search-and-replace') {
	$rowspan = ' rowspan="2"';
	$isSearchAndReplace = true;
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
		$('#BtnTypeDryrun').on('click', function(){
			$('#TextReplaceType').val('dryrun');
//			if (!confirm('検索置換対象の指定内容で、検索語句を一括検索し、置換後の状態を表示します。\n宜しいですか？')) {
//				return false;
//			}
		});

		// 置換＆保存ボタン 実行時
		$('#BtnTypeSearchAndReplace').on('click', function(){
			$('#TextReplaceType').val('search-and-replace');
			$('#BtnTypeSearchAndReplaceDialog').dialog({
				modal: true,
				title: '置換＆保存',
				width: 400,
				buttons: {
					"キャンセル": function() {
						$(this).dialog("close");
					},
					"OK": function() {
						$(this).dialog("close");
						$("#TextReplaceAdminIndexForm").submit();
					}
				}
			});
			return false;
		});

		// 検索置換対象のチェックボックスを全てチェックする
		$('#TextReplaceCheckAll').on('click', function(){
			if ($(this).prop('checked')) {
				$('.target-check input[type=checkbox]').prop('checked', true);
			} else {
				$('.target-check input[type=checkbox]').prop('checked', false);
			}
		});

		// 検索置換対象のモデル単位で、チェックボックスを全てチェックする／チェック外す
		$('.target-check fieldset legend').on('click', function(){
			$(this).parent().find('.checkbox input[type=checkbox]').each(function(){
				isChecked = false;
				if ($(this).prop('checked')) {
					isChecked = true;
				}
			});
			if (isChecked) {
				$(this).parent().find('.checkbox input[type=checkbox]').prop('checked', false);
			} else {
				$(this).parent().find('.checkbox input[type=checkbox]').prop('checked', true);
			}
		});

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

		// 検索フィールドの結果テキストの検索語句にマークを付ける
		$('.box-field-result-all').each(function(){
			$(this).find('.box-field-result').find('.replace-before').each(function(){
				var text = $(this).html();
				var patternVal = $('#TextReplaceSearchPattern').val();
				//console.log(patternVal);
				var pattern = new RegExp(patternVal);
				//console.log(pattern);
				//$(this).html(text.replace(pattern, "<strong>$1</strong>"));
			});
		});

	});
</script>

<style type="text/css">
	.target-check fieldset legend {
		font-weight: bold;
		cursor: pointer;
	}
	.target-check .checkbox {
		margin-right: 10px;
		margin-bottom:14px;
	}
</style>

<div id="BtnTypeSearchAndReplaceDialog" title="置換＆保存" class="display-none">
	<p><strong>置換＆保存を実行します。よろしいですか？</strong></p><br />
	<p>※ 実行前に必ずDBのバックアップを取ってください。<br />※ 検索語句を置換後の内容で一括変換します。</p>
</div>

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
						<li>正規表現を利用する際には、文字列両端にデリミタ「/〜/」を付けてください。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('TextReplace.search') ?>
				<br />
				<?php echo $this->BcForm->input('TextReplace.search_regex', array('type' => 'checkbox', 'label' => '正規表現を利用する（デリミタ「/〜/」を付けてください）')); ?>
				<?php echo $this->BcForm->error('TextReplace.search_regex') ?>
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
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.check_all', '全て選択') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('TextReplace.check_all', array('type' => 'checkbox', 'label' => '検索置換対象全てにチェックを入れる')); ?>
				<?php echo $this->BcForm->error('TextReplace.check_all') ?>
				<small>「モデル名」をクリックすると、クリックしたモデル名の範囲全てに対して チェックを入れる／外す ことができます。</small>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('TextReplace.replace_target', '検索置換対象の指定') ?></th>
			<td class="col-input target-check">
				<?php echo $this->BcForm->input('TextReplace.replace_target', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $replaceTarget)); ?>
				<?php echo $this->BcForm->error('TextReplace.replace_target') ?>
			</td>
		</tr>
	</tbody>
</table>


<?php echo $this->BcForm->input('TextReplace.type', array('type' => 'hidden', 'value' => '')) ?>
<div class="submit">
	<?php $confirmMessageSearch = '検索置換対象の指定内容で、検索語句を一括検索します。\n宜しいですか？' ?>
	<?php //echo $this->BcForm->submit('検索', array('div' => false, 'id' => 'BtnTypeSearch', 'class' => 'button', 'onClick'=>"return confirm('$confirmMessageSearch')")) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'id' => 'BtnTypeSearch', 'class' => 'button')) ?>

	<?php $confirmMessageDryrun = '検索置換対象の指定内容で、検索語句を一括検索し、置換後の状態を表示します。\n宜しいですか？' ?>
	<?php //echo $this->BcForm->submit('置換確認', array('div' => false, 'id' => 'BtnTypeDryrun', 'class' => 'button', 'onClick'=>"return confirm('$confirmMessageDryrun')")) ?>
	<?php echo $this->BcForm->submit('置換確認', array('div' => false, 'id' => 'BtnTypeDryrun', 'class' => 'button')) ?>
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
					<?php if ($isReplace): ?>
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

<?php if ($isReplace): ?>
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


<?php // モデル毎のループ ?>
<?php foreach ($datas as $modelName => $modelData): ?>
<div class="box-model-result">
<h3><?php echo $this->BcText->arrayValue($modelName, $this->TextReplace->getModelList()) ?>（<?php echo $modelName; ?>）</h3>
	<?php $fieldCount = 0; ?>
	<div class="box-field-result-all">
	<?php // フィールド毎のループ ?>
	<?php foreach ($modelData as $fieldName => $fieldValue): ?>
		<?php $fieldCount = $fieldCount + count($fieldValue); ?>
		<div class="box-field-result">
		<table cellpadding="0" cellspacing="0" class="list-table form-table">
			<tbody>
				<?php // フィールド毎にヒットした結果のループ ?>
				<?php foreach ($fieldValue as $num => $result): ?>
				<tr>
					<th class="col-head"<?php echo $rowspan; ?>>
						<?php echo TextReplaceUtil::getFieldTitle($modelName, $fieldName) ?>
						<?php //echo $fieldName; ?>（ID:<?php echo $result[$modelName]['id']; ?>）
					</th>
					<?php if ($isReplace): ?>
					<td class="col-input" nowrap="nowrap"<?php echo $rowspan; ?>>
						<label for="TextReplaceTarget<?php echo $modelName . $result[$modelName]['id']; ?>">
							<input type="checkbox" name="data[ReplaceTarget][][<?php echo $modelName; ?>.<?php echo $fieldName; ?>]" value="<?php echo $result[$modelName]['id']; ?>" id="TextReplaceTarget<?php echo $modelName . $result[$modelName]['id']; ?>">
						</label>
					</td>
					<?php endif ?>
					<td class="col-input replace-before" style="width: 100%;">
						<?php echo $this->BcBaser->mark($query, h($result[$modelName][$fieldName])) ?>
					</td>
				</tr>
				<?php if ($isReplace || $isSearchAndReplace): ?>
				<tr>
					<td class="col-input replace-after">
						<?php echo $this->BcBaser->mark($query,
							h(TextReplaceUtil::getReplaceData($result[$modelName][$fieldName],
								$this->request->data['TextReplace']['search_pattern'],
								$this->request->data['TextReplace']['replace_pattern'],
								array('search_regex' => $this->request->data['TextReplace']['search_regex'])
						))) ?>
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

<?php if ($isReplace): ?>
<div class="submit">
	<?php echo $this->BcForm->submit('置換＆保存', array('div' => false, 'id' => 'BtnTypeSearchAndReplace', 'class' => 'button')) ?>
</div>
<?php endif ?>

<?php endif ?>

<?php echo $this->BcForm->end() ?>
