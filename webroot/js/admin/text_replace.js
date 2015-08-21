/**
 * [ADMIN] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
$(function () {

	/**
	 * 検索・置換ボタン実行時の操作
	 */
	// 検索ボタン 実行時
	$('#BtnTypeSearch').on('click', function(){
		$('#TextReplaceType').val('search');
	});

	// 置換確認ボタン 実行時
	$('#BtnTypeDryrun').on('click', function(){
		$('#TextReplaceType').val('dryrun');
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

	/**
	 * 検索・置換一覧を操作する
	 */
	// 検索結果一覧の見方を表示する
	$('#TextReplaceInsight').hide();
	$('#helpTextReplaceInsight').on('click', function(){
		$('#TextReplaceInsight').slideToggle();
	});

	// モデル別の検索結果を開閉する
	$('.box-model-result h3').on('click', function(){
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

	// @TODO 検索フィールドの結果テキストの検索語句にマークを付ける
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
