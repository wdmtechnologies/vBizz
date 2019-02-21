<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}


?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('BUG_TRACKER'); ?></h1>
	</div>
</header>

<div class="content_part">

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=bug'); ?>" method="post" name="adminForm" id="adminForm">

	<div class="adminlist filter">
		<div class="filet_left">
			<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
			<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();">
			<i class="fa fa-search"></i></button>
			<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
		</div>
	</div>

	<div id="editcell">
		<table class="adminlist table">
			<thead>
				<tr>
					<th><?php echo JHTML::_('grid.sort', 'SUBJECT', 'subject', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'FROM', 'from_name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'DATE', 'date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				</tr>
			</thead>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
				$row = &$this->items[$i];
				$checked 	= JHTML::_('grid.id',   $i, $row->id );
				$link 		= JRoute::_( 'index.php?option=com_vbizz&view=bug&task=edit&cid[]='. $row->id );
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					
					<td align="center"><a href="<?php echo $link; ?>"><?php echo $row->subject; ?></a></td>
					
					<td align="center"><?php echo $row->from_name; ?><br><?php echo $row->from_email; ?></td>
					
					<td align="center"><?php echo $row->mail_date; ?></td>
					
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			<tfoot>
				<tr>
				  <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	 
	<input type="hidden" name="option" value="com_vbizz" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="bug" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	
</form>
</div>

</div>
</div>