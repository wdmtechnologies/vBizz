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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();
/* $query = 'CREATE TABLE IF NOT EXISTS `#__vbizz_comment_section` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(150) NOT NULL,
  `section_id` int(100) NOT NULL,
  `from_id` int(100) NOT NULL,
  `to_id` int(100) NOT NULL,
  `msg` text NOT NULL,
  `date` datetime NOT NULL,
  `created_by` int(100) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
$db->setQuery($query);
$db->execute();
jexit(); */

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->widget_acl->get('addaccess');
$edit_access = $this->config->widget_acl->get('editaccess');
$delete_access = $this->config->widget_acl->get('deleteaccess');

if($add_access) {
	$addaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$add_access))
		{
			$addaccess=true;
			break;
		}
	}
} else {
	$addaccess=false;
}

if($edit_access) {
	$editaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$edit_access))
		{
			$editaccess=true;
			break;
		}
	}
} else {
	$editaccess=false;
}

if($delete_access) {
	$deleteaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$delete_access))
		{
			$deleteaccess=true;
			break;
		}
	}
} else {
	$deleteaccess=false;
}
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.vbizz.widgetlisting.list.';
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_status = $mainframe->getUserStateFromRequest( $context.'published','published','desc','word' );
		$this->widgetfor = $mainframe->getUserStateFromRequest( $context.'widgetfor', 'widgetfor', '', 'int' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$published = array();
		$search = JString::strtolower( $search );
		$pubs = array(JText::_('SELECT_STATUS'), JText::_('PUBLISHED'),JText::_('UNPUBLISHED'));
		$pub = array('', 'publish','unpublish');
		for($i=0;$i<count($pub);$i++)
		$published[] = JHTML::_('select.option',$pub[$i], $pubs[$i] );
		 $published = JHTML::_('select.genericlist', $published, 'published', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_status );
//get date format from configuration
$format = $this->config->date_format;

		//get currency format from configuration
$currency_format = $this->config->currency_format;



?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('COM_VBIZZ_V_LISTING'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz&layout=widgetlisting'); ?>" method="post" name="adminForm" id="adminForm">

<?php if($addaccess || $editaccess || $deleteaccess) { ?>
<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
								
						<?php if($addaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-new">
                            <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                            <span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
                        </div>
                        <?php } ?>
                        <?php if($editaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-edit">
						<span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
						<span class="fa fa-edit"></span> <?php echo JText::_('EDIT'); ?></span>
					</div>
					<?php } ?>
						<?php if($deleteaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-delete">
                        <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
                        <span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
                        </div>
                        <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="adminlist filter">
<div class="filet_left filter_block-a">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="<?php echo $search;?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

<?php if(VaccountHelper::checkOwnerGroup()) { ?>
<div class="filter_right filter_block-b fltre_view">
	<label><?php echo JText::_( 'VIEW_AS' ); ?></label>
	<?php echo VaccountHelper::vbizzusergroup('widgetfor',$this->widgetfor, 'class="inputbox" size="1" onchange="submitform( );"', true, 'widgetfor');
   echo $published;

	?>
	
</div>
<?php } ?>

</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
				
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
				
                <th><?php echo JHTML::_('grid.sort', 'TITLE', 'name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'WIDGET_FOR', 'widgetfor', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
			    <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'STATUS', 'published', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
               
            </tr>
        </thead>
    <?php
    $k = 0;
	 
    for ($i=0, $n=count( $this->widgetlisting ); $i < $n; $i++)	{
		$row = &$this->widgetlisting[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$item_registry = new JRegistry;
		$item_registry->loadString($row->access);
		$tran_access = $item_registry->get('access_interface');
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=widget&cid[]='.$row->id.'&section=listing' );
		
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
           <td><?php echo $checked; ?></td>
            
            <td>
			<a href="<?php echo $link; ?>"><?php echo empty($row->name)?JText::_("COM_EDIT_VBIZZ_WIDGET"):JText::_($row->name); ?>
			</a>
            </td>
			
			
            <td><?php echo VaccountHelper::getGroupName($tran_access);?></td>
			<td class="publish_unpublish center"><?php  echo JHtml::_('jgrid.published',$row->published,$i);?></td>
            
            
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
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="layout" value="widgetlisting" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
</form>
</div>


