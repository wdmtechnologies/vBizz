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

//$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add access
$add_access = $this->config->account_acl->get('addaccess');


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

 ?>

<script type="text/javascript">

jQuery(function() {
	
	var availableBalance = 0;
	jQuery(document).on('change', 'select[name="account_id"]', function()	{
		jQuery('#avail-bal').remove();
		var account = jQuery(this).val();
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz','view':'deposit', 'task':'getAvailableBalance', 'tmpl':'component', 'account':account},
			
			beforeSend: function() {
				jQuery(".loading").css('display','inline-block');
			},
			complete: function()      {
				jQuery(".loading").hide();
			},
			success: function(data){
				if(data.result=="success"){
					availableBalance = data.balance;
					var html = '<div id="avail-bal" style=color:#FF0000;><?php echo JText::_('AVAILABLE_BALANCE'); ?> : '+data.balance+'</div>';
					jQuery("#from_acc").append(html);
			
				}
			}
		});
		
	});
	
	jQuery(document).on('click', '#transfer-money', function()	{
		
		var account_id = jQuery('select[name="account_id"]').val();
		
		var amount = jQuery('input[name="amount"]').val();
		
		if(account_id == "")	{
			alert("<?php echo JText::_('SELECT_ACCOUNT'); ?>");
			return false;
		}
		
		if(amount == "")	{
			alert("<?php echo JText::_('ENTER_AMOUNT_TO_DEPOSIT'); ?>");
			return false;
		}
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: { 'option':'com_vbizz','view':'deposit', 'task':'save', 'tmpl':'component', 'account_id':account_id, 'amount':amount },
			
			beforeSend: function() {
				jQuery(".loading").css('display','inline-block');
			},
			complete: function()      {
				jQuery(".loading").hide();
			},
			success: function(data){
				if(data.result=="success"){
					jQuery('#avail-bal').remove();
					jQuery('select[name="account_id"]').val('').trigger('liszt:updated');
					jQuery('input[name="amount"]').val('');
					alert(data.msg);
				} else {
					alert(data.msg);
				}
			}
		});
		
		return false;
		
	});
	
});

</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('DEPOSIT'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=deposit'); ?>" method="post" name="adminForm" id="adminForm">

<div class="btn-wrapper transfer-money"  id="toolbar-cancel">
	<span onclick="Joomla.submitbutton('cancel')" class="btn btn-success">
	<span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK'); ?></span>
</div>


<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_DEPOSIT_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">

<div class="loading"><div class="loading-icon"><div></div>
</div></div>

<fieldset class="adminform">

<table class="adminform table table-striped">
    <tbody>
        <tr id="from-acc">
            <th><label class="hasTip" title="<?php echo JText::_('DEPOSITACCOUNTTXT'); ?>">
				<?php echo JText::_('ACCOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</th>
            <td id="from_acc">
                <select name="account_id" id="account_id">
                <option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
                <?php	for($i=0;$i<count($this->account);$i++)	{	?>
                <option value="<?php echo $this->account[$i]->id; ?>" <?php if($this->account[$i]->id==$this->item->account_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->account[$i]->account_name); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
		
		<tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ENTERTRANSAMTTXT'); ?>">
                <?php echo JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="amount" id="amount" value="<?php echo $this->item->amount;?>"/></td>
        </tr>
		
		<tr>
            <td></td>
            <td>
				<div class="transfer-money">
					<a href="javascript:void();" class="btn" id="transfer-money">
						<span class="fa fa-send"></span> <?php echo JText::_('DEPOSIT'); ?></span>
					</a>
				</div>
				
			</td>
        </tr>
		
    </tbody>
</table>



</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="deposit" />
</form>
</div>

<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>