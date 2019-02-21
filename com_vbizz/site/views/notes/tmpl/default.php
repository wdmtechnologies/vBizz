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
JHTML::_('behavior.calendar');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
?>


<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ACTIVITY_LOG'); ?></h1>
	</div>
</header>
<div class="content_part">

<form action="<?php JRoute::_( 'index.php?option=com_vbizz&view=notes' ); ?>" method="post" name="adminForm" id="adminForm">

<div class="v-leave">
	<span onclick="Joomla.submitbutton('clearLog')" class="btn btn-small btn-success">
	<span class="fa fa-remove"></span> <?php echo JText::_('CLEAR_LOG'); ?></span>
</div>


<div class="adminlist filter filter_note">
	<div class="filet_left">
	<input type="text" name="search" id="search" placeholder="<?php echo JText::_('Search'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area" style="width:50%;" />
	<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
	<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit(); document.getElementById('filter_begin').value='';this.form.fireEvent('submit');this.form.submit(); document.getElementById('filter_end').value='';this.form.fireEvent('submit');this.form.submit();"><i class="fa fa-remove"></i></button>
	</div>
	<div class="filter_right">
		<div class="begin_date">
			<?php echo JHTML::_('calendar', $this->state->get['filter_begin'], "filter_begin" , "filter_begin", '%Y-%m-%d', " placeholder='".JText::_( 'BEGIN_DATE' )."'"); ?>
		</div>
		<div class="end_date">
			<?php echo JHTML::_('calendar', $this->state->get['filter_end'], "filter_end" , "filter_end", '%Y-%m-%d', " placeholder='".JText::_( 'END_DATE' )."'"); ?>
		</div>
		
		<button class="btn" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
		
	</div>
</div>

<div id="editcell">
    <table class="adminlist table">
	
		<?php if((count( $this->items ))<1) { ?>
		<tr>
			<td><span><?php echo JText::_('NO_ACTIVITY_TO_SHOW'); ?></span></td>
		</tr>
		<?php } else { ?>
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
				<th><?php echo JText::_( 'DATE' ); ?></th>
                <th><?php echo JText::_( 'ACTIVITY' ); ?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$format = $this->config->date_format.', g:i A';
		$datetime = strtotime($row->created);
		$created = date($format, $datetime );
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
			
			<td><?php echo $created; ?></td>
            
            <td><?php echo $row->comments; ?></td>
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
		<?php } ?>
    </table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="notes" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
