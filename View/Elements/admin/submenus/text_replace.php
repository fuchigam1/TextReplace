<?php
/**
 * [ADMIN] サブメニュー
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			TextReplace
 * @license			MIT
 */
?>
<tr>
	<td>
		<ul>
			<?php if ($this->request->params['controller'] == 'text_replaces'): ?>
				<li><?php $this->BcBaser->link('テキスト置換処理',
						array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'),
						array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
			<?php else: ?>
				<li><?php $this->BcBaser->link('テキスト置換処理',
						array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index')); ?></li>
			<?php endif ?>
			<li><?php $this->BcBaser->link('テキスト置換ログ',
					array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replace_logs', 'action' => 'index')); ?></li>
			<?php if ($this->request->params['controller'] == 'text_replaces'): ?>
				<li><?php $this->BcBaser->link('固定ページテンプレート書出',
						array('admin' => true, 'plugin' => null, 'controller' => 'pages', 'action' => 'write_page_files'),
						array('confirm' => 'データベース内のページデータを、' . 'テーマ ' . Inflector::camelize($this->BcBaser->siteConfig['theme']) . " のページテンプレートとして全て書出します。\n本当によろしいですか？")) ?></li>
				<li><?php $this->BcBaser->link('サーバーキャッシュ削除',
						array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache'),
						array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
				<li><?php $this->BcBaser->link('データメンテナンス',
						array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance'),
						array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
			<?php endif ?>
		</ul>
	</td>
</tr>
