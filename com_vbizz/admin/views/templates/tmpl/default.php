<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
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
//echo '<pre>';print_r($this->templates);
?>
<?php if (!empty( $this->sidebar)) : ?>   
		<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;?>
<form action="index.php?option=com_vbizz&view=templates" method="post" name="adminForm" id="adminForm">
<legend><?php echo JText::_('EMAIL_ETEMPLATES'); ?></legend>
<div class="filter">
<div class="search_buttons">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
<div class="filter-select fltrt">
<?php echo $this->pagination->getLimitBox(); ?>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'TEMPLATE_NAME', 'template_name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="center" width="50"><?php echo JHTML::_('grid.sort', 'DEFAULT', 'default_tmpl', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone center" width="30"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=templates&task=edit&cid[]='.$row->id );
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            <td><a href="<?php echo $link; ?>"><?php echo $row->template_name; ?></a></td>
            
            <td class="center featured_unfeatured" width="50"><?php echo JHtml::_('jgrid.isdefault', $row->default_tmpl, $i);?></td>
            
            <td class="hidden-phone center" width="30"><?php echo $row->id; ?></td>
            
        
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
        <tfoot>
            <tr>
                <td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="templates" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>