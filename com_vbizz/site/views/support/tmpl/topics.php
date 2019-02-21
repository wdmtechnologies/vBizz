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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->support_acl->get('addaccess');
$edit_access = $this->config->support_acl->get('editaccess');
$delete_access = $this->config->support_acl->get('deleteaccess');

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
	$addaccess=true;
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
	$editaccess=true;
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
	$deleteaccess=true;
}



$category = JRequest::getInt('category',0);

?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('SUPPORT_FORUM'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=support&layout=topics&category='.$category); ?>" method="post" name="adminForm" id="adminForm">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
					
					<div class="btn-wrapper"  id="toolbar-arrow-left-4">
						<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=support');?>" class="btn btn-small">
						<span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK'); ?></a>
					</div>					
					<?php if($addaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-new">
                            <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                            <span class="fa fa-plus"></span> <?php echo JText::_('NEW_TOPIC'); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="adminlist filter">
	<div class="filet_left">
		<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
	</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                
				 <th><?php echo JText::_('TOPICS');?></th>
              
            </tr>
        </thead>
		</table>
	 <table class="adminlist table">	
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=support&layout=replies&category='.$category.'&topic='.$row->id );
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center"><?php echo $this->pagination->getRowOffset($i);?></td>
            <td><strong><?php echo $row->views.'</strong> '.JText::_('COM_VBIZZ_REPLIES'); ?></td>
            <td>
            <?php if ($editaccess) : ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->subject; ?></a>
            <?php else : ?>
            	<?php echo $row->subject; ?>
            <?php endif; 
			
			$date_html = VaccountHelper::date_diff($row->created);
			  $check_statuss = JFactory::getUser($row->created_by);
              $check_status = $check_statuss->guest;
   
			?>
			<div class="ktopic-details-kcategory">
			<span class="ktopic-posted-time" title="<?php echo $row->created ;?>">
			<?php echo JText::_('COM_VBIZZ_TOPIC_STARTED').$date_html.JText::_('COM_VBIZZ_TOPIC_STARTED_AGO');?></span>
			<span class="ktopic-by ks"><?php echo JText::_('COM_VBIZZ_TOPIC_STARTED_BY');?> <a class="kwho-user" href="javascript:void(0);" title="View Villalba Online's Profile" rel="nofollow"><?php echo $check_statuss->name;?></a><?php echo JText::_('COM_VBIZZ_TOPIC_STARTED_STATUS').(!$check_status?JText::_('online'):JText::_('logout'));?></a></span>
		  </div>
            </td>
			
            <td><?php $last_update_status = VaccountHelper::LastReply($row->id, $category) ; ?>
             <div class="last_update_info">
			 <div class="last_update_info_user"><?php echo JText::_('COM_VBIZZ_LAST_UPDATED_INFO_BY').(isset($last_update_status->created_by->name)?$last_update_status->created_by->name:'');?></div>
			 <div class="last_update_info_time"><?php echo isset($last_update_status->created)?VaccountHelper::date_diff($last_update_status->created).JText::_('COM_VBIZZ_TOPIC_STARTED_AGO'):'';?></div>
			 </div></td>
            
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
        <tfoot>
            <tr>
                <td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="category" value="<?php echo $category; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>
