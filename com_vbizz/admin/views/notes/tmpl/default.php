<?php 
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDM Technologies
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

$user = JFactory::getUser();
?>
<?php if (!empty( $this->sidebar)) : ?>   
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;?>
<form action="index.php?option=com_vbizz&view=notes" method="post" name="adminForm" id="adminForm">
<legend><?php echo JText::_('NOTES'); ?></legend>
<div class="filter">
<div class="search_buttons">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_type').value='';this.form.submit();
document.getElementById('filter_view').value='';this.form.submit();"><i class="icon-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
<div class="filter-select fltrt">
<?php
echo $this->lists['types'];
echo $this->lists['viewlt'];
?>
<?php echo $this->pagination->getLimitBox(); ?>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'COMMENTS', 'comments', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'TYPE', 'type', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'USERS', 'created_by', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'CREATED_ON', 'created', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone center"><?php echo JHTML::_('grid.sort', 'ITEMID', 'itemid', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone center"><?php echo JHTML::_('grid.sort', 'VIEW', 'views', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone center" width="30"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$canEdit = $user->authorise('core.edit', 'com_vbizz');
		$canChange = $user->authorise('core.edit.state', 'com_vbizz');
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=notes&task=edit&cid[]='. $row->id );
		$saved_date = $row->created;
		$datetime = strtotime($saved_date);
		$created = date('M j Y, g:i A', $datetime );
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            <td>
            <?php if ($canEdit) : ?>
            	<a href="<?php echo $link; ?>"><?php echo JHTML::_('string.truncate', ($row->comments), 50); ?></a>
            <?php else : ?>
            	<?php echo JHTML::_('string.truncate', ($row->comments), 50); ?>
            <?php endif; ?>
            </td>
            
            <td class="hidden-phone">
            <?php 
				if($row->type=="notification") {
					echo JText::_('NOTIFICATION');
				}else if($row->type=="data_manipulation") {
					echo JText::_('DATA_MANIPULATION');
				}else if($row->type=="configuration") {
					echo JText::_('CONFIGURATION');
				} else if($row->type=="import_export") {
					echo JText::_('IMPORT_EXPORT');
				} else if($row->type=="recurring") {
					echo JText::_('RECURRING');
				}  
			
			?>
            </td>
            
            <td><?php echo $row->username; ?></td>
            
            <td class="hidden-phone"><?php echo $created; ?></td>
            
            <td class="hidden-phone center"><?php if($row->itemid) echo $row->itemid; else echo ""; ?></td>
            
            <td class="hidden-phone center"><?php echo $row->views; ?></td>
            
            <td class="hidden-phone center" width="30"><?php echo $row->id; ?></td>
            
        
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
<input type="hidden" name="view" value="notes" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>