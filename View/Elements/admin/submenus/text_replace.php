<?php
/**
 * [ADMIN] サブメニュー
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
?>
<tr>
	<th>テキスト置換管理メニュー</th>
	<td>
		<ul>
			<?php if ($this->request->params['controller'] == 'text_replaces'): ?>
				<?php $this->BcBaser->link('<li>テキスト置換処理</li>',
						array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'),
						array(), '検索条件は保存されません。よろしいですか？', false); ?>
			<?php else: ?>
				<?php $this->BcBaser->link('<li>テキスト置換処理</li>',
						array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index')); ?>
			<?php endif ?>
				<?php $this->BcBaser->link('<li>テキスト置換ログ</li>',
					array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'index')); ?>

			<?php if ($this->request->params['controller'] == 'text_replace_logs'): ?>
				<?php $this->BcBaser->link('<li>テキスト置換ログCSVダウンロード</li>',
					array('plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'download_csv')) ?>
			<?php endif ?>
		</ul>
	</td>
</tr>
