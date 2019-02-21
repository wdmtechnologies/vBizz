<?php 
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');


?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('SEARCH'); ?></h1>
	</div>
</header>

<div class="content_part">
	<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=search'); ?>" method="post" name="adminForm" id="adminForm">
		
		<div class="search">
			<?php for($i=0;$i<count($this->items);$i++) {

				$search_result = $this->items[$i];
				
				$category = $search_result[0];
			
				$view = $search_result[1];
			
				$title = $search_result[2];

			?>
			<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view='.$view.'&search='.$title); ?>">
			<div class="search-result">
				<div class="search-title">
					<h2><?php echo $title; ?></h2>
				</div>
				<div class="search-category">
					<span><span><h3 class="category-title"><?php echo JText::_('CATEGORY'); ?>: <?php echo $category; ?></h3></span>  <span></span></span>
				</div>
			</div>
			</a>
			<?php } ?>
		</div>
		<input type="hidden" name="option" value="com_vbizz" />
		<input type="hidden" name="view" value="search" />
	</form>
</div>

