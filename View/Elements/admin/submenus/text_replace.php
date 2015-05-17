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
					array('plugin' => 'text_replace', 'controller' => 'text_replaces', 'action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('サーバーキャッシュ削除',
					array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache')) ?></li>
			<li><?php $this->BcBaser->link('データメンテナンス',
					array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance')) ?></li>
		</ul>
	</td>
</tr>
