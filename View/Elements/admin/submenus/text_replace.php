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
			<li><?php $this->BcBaser->link('テキスト置換処理',
					array('admin' => true, 'plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index'),
					array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
			<li><?php $this->BcBaser->link('サーバーキャッシュ削除',
					array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache'),
					array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
			<li><?php $this->BcBaser->link('データメンテナンス',
					array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance'),
					array(), '検索条件は保存されません。よろしいですか？', false); ?></li>
		</ul>
	</td>
</tr>
