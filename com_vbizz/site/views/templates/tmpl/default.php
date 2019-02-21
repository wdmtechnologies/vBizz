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
//echo '<pre>';print_r($this->templates);
?>
<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php echo JText::_('DEF_INVOICE'); ?></h1>
		</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=templates');?>" method="post" name="adminForm" id="adminForm">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
					<?php if($this->editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-edit">
                        <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
                        <span class="icon-edit"></span> <?php echo JText::_('EDIT'); ?></span>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<div class="adminlist table filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'TEMPLATE_NAME', 'template_name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JText::_( 'PREVIEW' ); ?></th>
                <?php if($this->editaccess) { ?>
                <th><?php echo JHTML::_('grid.sort', 'DEFAULT', 'default_tmpl', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <?php } ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
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
            
            <?php if($this->editaccess) { ?>
            <td><a href="<?php echo $link; ?>"><?php echo $row->template_name; ?></a></td>
            <?php } ?>
            
            <td class="hidden-phone"><a class="modal" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=templates&layout=setdefault&tmpl=component&ids='.$row->id.'&ext='.$row->image_ext;?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
            <span class="hasTip" title="<?php echo JText::_('CLICK_TO_PREVIEW'); ?>"><?php echo JText::_('SHOW_PREVIEW');; ?></span></a>
        	</td>
            
            <?php if($this->editaccess) { ?>
            <td><?php echo JHtml::_('jgrid.isdefault', $row->default_tmpl, $i);?></td>
            <?php } ?>
            
            <td class="hidden-phone"><?php echo $row->id; ?></td>
            
        
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
<input type="hidden" name="view" value="templates" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

</div>