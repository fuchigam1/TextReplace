/**
 * [ADMIN] TextReplace
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
$(window).load(function () {
	$("#TextReplaceCheckAll").focus();
});

$(function () {

	/**
	 * 検索時の正規表現利用チェック時の操作
	 */
	searchRegexCheckHandler();
	$('#SearchRegexChecked, label[for=TextReplaceSearchRegex], #TextReplaceSearchRegex').on('click', function () {
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
	$('.target-check legend').each(function () {
		var $input = $('<span class="bca-checkbox"><input type="checkbox" class="model-range bca-checkbox__input" /><label class="bca-checkbox__label">' + $(this).html() + '</label></span>').attr("checked", false);
		// legend の対象テキスト: console.log($(this).html());
		$(this).html($input);
	});

	// 「対象」配下全てにチェックボックスが入ってる場合は、その「対象」にチェックを入れる（検索・置換実行後のための制御）
	$('.target-check fieldset legend').each(function () {
		var isAllChecked = true;
		$(this).parent().find('label input[type=checkbox]').each(function () {
			if (!$(this).prop('checked')) {
				isAllChecked = false;
				return false;
			}
		});
		if (isAllChecked) {
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', true);
		} else {
			// 動的に付与した「対象」チェックボックスを操作する
			$(this).parent().find('input.model-range').prop('checked', false);
		}
	});


	// 検索置換対象のチェックボックスを全てチェックする
	$('#TextReplaceCheckAll').on('click', function () {
		if ($(this).prop('checked')) {
			$('.target-check input[type=checkbox]').prop('checked', true);
		} else {
			$('.target-check input[type=checkbox]').prop('checked', false);
		}
	});

	// 検索置換対象のモデル単位で、チェックボックスを全てチェックする／チェック外す
	$('.target-check fieldset legend').on('click', function () {
		var isChecked = $(this).find('input[type=checkbox]').prop('checked');
		if (isChecked) {
			// 動的に付与した「対象」チェックチェックがついてるときは、配下の対象チェックをオフにする
			$(this).find('input.model-range').prop('checked', false);
			$(this).parent().find('input[type=checkbox]').prop('checked', false);
		} else {
			// 動的に付与した「対象」チェックがついてないときは、配下の対象チェックをオンにする
			$(this).find('input.model-range').prop('checked', true);
			$(this).parent().find('input[type=checkbox]').prop('checked', true);
		}
	});


	/**
	 * 検索・置換ボタン実行時の操作
	 */
	// 検索ボタン 実行時
	$('#BtnTypeSearch').on('click', function () {
		$('#TextReplaceType').val('search');
	});

	// 置換確認ボタン 実行時
	$('#BtnTypeDryrun').on('click', function () {
		$('#TextReplaceType').val('dryrun');
	});

	// 置換＆保存ボタン 実行時
	$('.btn-type-search-and-replace').on('click', function () {
		$('#TextReplaceType').val('search-and-replace');
		$('#BtnTypeSearchAndReplaceDialog').dialog({
			modal: true,
			title: '置換＆保存',
			width: 400,
			buttons: {
				"キャンセル": function () {
					$(this).dialog("close");
				},
				"OK": function () {
					$(this).dialog("close");
					// 検索置換時、414 Request-URI too large が出る可能性が高いため post 送信に切替える
					$('#TextReplaceIndexForm').attr({'method': 'post'});
					$.bcUtil.showLoader();
					$("#TextReplaceIndexForm").submit();
				}
			}
		});
		return false;
	});

	/**
	 * 検索・置換一覧を操作する
	 */

	// モデル別の検索結果数を表示する
	if ($('.box-field-result-all').length) {
		$('.box-field-result-all').each(function () {
			var count = $(this).find('.field-count').html();
			$(this).parent().children('h3').append(count + '件');
		});
	}

	if ($('.box-model-result').length) {
		// 置換対象指定チェックボックスを全てチェックする
		if ($('#TextReplaceCheckBoxModelResult').prop('checked')) {
			$('.box-model-result input[type=checkbox]').prop('checked', true);
		}
		$('#TextReplaceCheckBoxModelResult').on('click', function () {
			if ($(this).prop('checked')) {
				$('.box-model-result input[type=checkbox]').prop('checked', true);
			} else {
				$('.box-model-result input[type=checkbox]').prop('checked', false);
			}
		});

		// モデル別の検索結果を開閉する
		$('.box-model-result h3').on('click', function () {
			$(this).next().slideToggle();
		});
	}

	// 置換確認の結果一覧で、モデル別の一括チェックを入れる
	if ($('.select-this-model').length) {
		$('.select-this-model').each(function () {
			if ($(this).prop('checked')) {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', true);
			}
		});
		$('.select-this-model').on('click', function () {
			if ($(this).prop('checked')) {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', true);
			} else {
				$(this).parents('.box-model-result').find('input[type=checkbox]').prop('checked', false);
			}
		});
	}

	// アクセス元URLがテキスト置換ログ画面の場合は、URLを表示する
	if ($('#IsAccessFromTextReplaceLogs').length) {
		$('#IsAccessFromTextReplaceLogs').show(3000);
	}

	if ($('#ReplaceInputSearchReplace').length) {
		$('#ReplaceInputSearchReplace').on('click', function (event) {
			event.preventDefault();
			var textReplaceSearchPattern = $('#TextReplaceSearchPattern').val();
			var TextReplaceReplacePattern = $('#TextReplaceReplacePattern').val();

			$('#TextReplaceSearchPattern').val();
			$('#TextReplaceReplacePattern').val();

			$('#TextReplaceSearchPattern').val(TextReplaceReplacePattern);
			$('#TextReplaceReplacePattern').val(textReplaceSearchPattern);
			return false;
		});
	}
});
