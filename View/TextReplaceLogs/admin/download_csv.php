<?php
/**
 * [View] TextReplace
 *
 * @link http://www.materializing.net/
 * @author arata
 * @package TextReplace
 * @license MIT
 */
?>
<?php $this->BcCsv->addModelDatas('TextReplaceLog', $datas) ?>
<?php $this->BcCsv->download($csvFileName) ?>
