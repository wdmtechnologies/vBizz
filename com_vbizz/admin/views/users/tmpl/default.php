<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if (!empty( $this->sidebar)) : ?>   
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;?>
<form action="index.php?option=com_vbizz&view=users" method="post" name="adminForm" id="adminForm">
<legend><?php echo JText::_('OWNERS'); ?></legend>
<div class="filter">
<div class="search_buttons">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="icon-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
</div>

<div id="editcell">
    <table class="adminlist table table-striped table-hover">
    <thead>
		<tr>
			<th width="10" align="left" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>

			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('NAME'), 'i.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('USERNAME'), 'i.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			
			<th class="hidden-phone">
				<?php echo JHTML::_('grid.sort', JText::_('EMAIL'), 'i.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			
			<th class="center">
				<?php echo JHTML::_('grid.sort', JText::_('ENABLED'), 'i.block', @$this->lists['order_Dir'], @$this->lists['order'] );?>
			</th>
			
			 <th class="hidden-phone center">
				<?php echo JHTML::_('grid.sort', JText::_('LAST_VISITED'), 'i.lastvisitDate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			 <th class="hidden-phone center">
				<?php echo JHTML::_('grid.sort', JText::_('REG_DATE'), 'i.registerDate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th class="hidden-phone center">
				<?php echo JHTML::_('grid.sort', JText::_('ID'), 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>

	<tfoot>
    <tr>
      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  	</tfoot>

    <?php
    $k = 0;
		
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row = $this->items[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );
		
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=users&task=edit&cid[]='.$row->id );
				
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td align="center"><?php echo $checked; ?></td>

            <td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
			
            <td><?php echo $row->username; ?></td>
			
            <td class="hidden-phone"><?php echo $row->email; ?></td>
            
            <td class="publish_unpublish center"><?php if($row->block) { ?> <span class="btn"><span class="icon-unpublish"></span></span> <?php } else { ?> <span class="btn"><span class="icon-publish"></span></span> <?php } ?></td>
			
            <td class="hidden-phone center"><?php if ($row->lastvisitDate != '0000-00-00 00:00:00'):?>
						<?php echo JHtml::_('date', $row->lastvisitDate, 'Y-m-d H:i:s'); ?>
					<?php else:?>
						<?php echo JText::_('JNEVER'); ?>
					<?php endif;?></td>
            <td class="hidden-phone center"><?php echo JHtml::_('date', $row->registerDate, 'Y-m-d H:i:s'); ?></td>
            <td class="hidden-phone center"><?php echo $row->id; ?></td>
            
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
    </table>
</div>
 <?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
