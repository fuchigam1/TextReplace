/**
 * [ADMIN] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
$(window).load(function() {
	$("#TextReplaceCheckAll").focus();
});

$(function () {

	/**
	 * 検索時の正規表現利用チェック時の操作
	 */
	searchRegexCheckHandler();
	$('#SearchRegexChecked, label[for=TextReplaceSearchRegex], #TextReplaceSearchRegex').on('click', function(){
		searchRegexCheckHandler();
	});

	function searchRegexCheckHandler() {
		if ($('#TextReplaceSearchRegex').prop('checked')) {
			$('#SearchRegexChecked').animate({opacity: 'show'}, 'slow');
		} else {
			$('#SearchRegexChecked').animate({opacity: 'hide'}, 'fast');
		}
	}

	/**
	 * 検索置換対象の指定の操作
	 */
	// 「対象」にチェックボックスを備える
	$('.target-check legend').each(function(){
		var $input = $('<input type="checkbox" class="model-range" />').attr("checked", false);
		// legend の対象テキスト: console.log($(this).html());
		$(this).prepend($input);
	});

	// 「対象」配下全てにチェックボックスが入ってる場合は、その「対象」にチェックを入れる（検索・置換実行後のための制御）
	$('.target-check fieldset legend').each(function(){
		$(this).parent().find('.checkbox input[type=checkbox]').each(function(){
			isChecked = false;
			if ($(this).prop('checked')) {
				isChecked = true;
			}
		});
		if (isChecked) {
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', true);
		} else {
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', false);
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
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', false);
		} else {
			$(this).parent().find('.checkbox input[type=checkbox]').prop('checked', true);
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', true);
		}
	});


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

	// 置換確認の結果一覧で、モデル別の一括チェックを入れる
	if ($('.select-this-model').length) {
		$('.select-this-model').each(function(){
			if ($(this).prop('checked')) {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', true);
			}
		});
		$('.select-this-model').on('click', function(){
			if ($(this).prop('checked')) {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', true);
			} else {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', false);
			}
		});
	}

//	 @TODO 検索フィールドの結果テキストの検索語句にマークを付ける
//	$('.box-field-result-all').each(function(){
//		$(this).find('.box-field-result').find('.replace-before').each(function(){
//			var text = $(this).html();
//			var patternVal = $('#TextReplaceSearchPattern').val();
//			console.log(patternVal);
//			var pattern = new RegExp(patternVal);
//			console.log(pattern);
//			$(this).html(text.replace(pattern, "<strong>$1</strong>"));
//		});
//	});

});
