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
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$tmpl = JRequest::getVar('tmpl','');
$db = JFactory::getDbo();

$query='select initial_balance FROM `#__vbizz_accounts` where id='.JRequest::getInt('accountid',0).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId()) ;
$db->setQuery($query);
$initial_balance = $db->loadResult();  
$initial_balance =  $this->openingBalance;
if($tmpl){ ?>
<style>
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.adminlist.table th a, .adminlist.table th {
    color: #333;
    font-size: 13px;
}
.table th {
    font-weight: bold;
}
.table th, .table td {
    border-top: 1px solid #ddd;
    line-height: 18px;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.table th {
    background: #eee none repeat scroll 0 0;
    border-bottom: 2px solid #ddd;
    font-weight: 500;
}
.open_balance {
    padding: 0 0 10px;
}
.open_balance, .closing_balance {
    text-align: right;
}

#vbizz table {
    border: 0 none;
    border-collapse: collapse;
}
.table {
    margin-bottom: 18px;
    width: 100%;
}
table {
    background-color: transparent;
    border-collapse: collapse;
    border-spacing: 0;
    max-width: 100%;
}
table {
    border-collapse: collapse;
    width: 100%;
}
.closing_balance {
    padding: 10px 0 0;
}
.open_balance, .closing_balance {
    text-align: right;
}
.front-page #vbizz .header {
    background: #fff none repeat scroll 0 0;
    border-bottom: 1px solid #e7ecf1;
    box-shadow: none;
    box-sizing: unset;
    float: left;
    height: 60px;
    margin: 0;
    min-height: inherit;
    padding: 0 1.5%;
    position: relative;
    text-align: left;
    width: 77%;
    z-index: 1;
}
.header {
    margin-bottom: 10px;
}
.front-page #vbizz .header h1 {
    color: #333;
    display: inline-block;
    font-size: 24px;
    font-weight: 300;
    line-height: 60px;
    margin: 0;
}
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.adminlist.table th a, .adminlist.table th {
    color: #333;
    font-size: 13px;
}
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.table th, .table td {
    border-top: 1px solid #ddd;
    line-height: 18px;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.open-bal-head, .closing-bal-head {
    color: #0088cc;
    font-size: 16px;
    font-weight: 700;
    padding: 0;
}
.open-bal-head, .open-bal-val, .closing-bal-head, .closing-bal-val {
    display: inline-block;
    vertical-align: middle;
}
.open-bal-val, .closing-bal-val {
    font-size: 18px;
    font-weight: 600;
}
.open-bal-head, .open-bal-val, .closing-bal-head, .closing-bal-val {
    display: inline-block;
    vertical-align: middle;
} 
</style>
 <?php }
if(!$tmpl){
?>
<script src="///cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.2.0/bootbox.min.js"></script>  

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	jQuery('#tmpl').remove();
	//alert(task);
	if(task == 'exportTrial') {
		var html = '<input type="hidden" name="tmpl" value="component" id="tmpl" />';
		jQuery('form[name="adminForm"]').append(html);
	}
	Joomla.submitform(task, document.id('adminForm'));
}
jQuery(function(){  
	jQuery(".notificationsetting").on("click", function(){
		
	var dialog = bootbox.dialog({
      title: "<?php echo JText::_("REPORT_SEND_TRANSECTION");?>",
	  size: 'small',
      message: "<div class='input-emailaddress'><span class='input-emailaddress-label'><?php echo JText::_("PLEASE_GIVE_EMAIL_ADDRESS_NOTIFICATION");?></span><span class='input-emailaddress-value'><input type='text' placeholder='<?php echo JText::_("PLEASE_GIVE_EMAIL_ADDRESS");?>' name='emailaddress' id='emailaddress' value=''><span class='loading'><i class='fa fa-spin fa-spinner'></i><?php echo JText::_("LOADING_TEXT");?></span></span></div>",
      buttons: {
      cancel: {
        label: "<?php echo JText::_('CLOSE');?>",
        className: 'btn-danger'
    },
    ok: {           
        label: "<?php echo JText::_('SEND');?>",
        className: 'btn-info',
        callback: function(){
			var value = jQuery("#emailaddress").val(); 
			if(value=="")
			{
			alert("<?php echo JText::_("PLEASE_GIVE_EMAIL_ADDRESS_MESSAGE");?>");
             return false;			
			}
           if(value!=""){
           jQuery.ajax({
			url: "",
		    type: "POST",
		    dataType:"json",
		    data: {"option":"com_vbizz", "view":"trial", "task":"sendNotification","emails":value, "tmpl":"component", "<?php echo JSession::getFormToken(); ?>":1, 'abase':1},
		
		    beforeSend: function() {
			jQuery(".loading").show();
		    },
		
			complete: function()
			{
				jQuery(".loading").hide();
			},
			success: function(data) 
		    {
				 bootbox.hideAll();
			}
			});
			return false;
			} 
        }
    }
}
});	
	
	});
	
});
</script>
<?php } ?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ACCOUNT_STATEMENT'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=trial'); ?>" method="post" name="adminForm" id="adminForm">

<?php if(!$tmpl){ ?>
<div class="adminlist table filter filter_trial">
<div class="filter_left">
<?php echo $this->lists['accounts']; ?>
<?php echo $this->lists['years']; ?>
<?php echo $this->lists['months']; ?>
<?php echo $this->lists['days']; ?>
<?php echo $this->lists['mode']; ?>
<?php if(VaccountHelper::checkOwnerGroup()) { ?>

<div class="btn-wrapper notificationsetting"  id="toolbar-send">
                        	<span class="btn btn-small">
                        	<span class="fa fa-send"></span> <?php echo JText::_('REPORT_SEND'); ?></span>
                        </div>


<?php } ?>
</div>

</div>
<?php } ?>

<div id="editcell">
	
	<?php if(!$this->acID) { ?>
	
	<div class="adminlist table">
		<span><h1><?php echo JText::_('NO_ACCOUNT_SELECTED_INFO'); ?></h1></span>
	</div>
		
	<?php } else { ?>
		<div class="open_balance">
			<div class="open-bal-head"><?php echo JText::_('OPENING_BALANCE'); ?></div>
			<div class="open-bal-val"><?php echo VaccountHelper::getValueFormat($this->openingBalance); ?></div>
		</div>
	
    <table class="adminlist table">
	
		
            <tr>
			    <th><?php echo JText::_('TRANSACTION_MODE');?></th>
                <th><?php echo JText::_('TRANSACTION');?></th>
				<th><?php echo JText::_('DEBIT');?></th>
				<th><?php echo JText::_('CREDIT');?></th>
				<th><?php echo JText::_('BALANCE');?></th>
            </tr>
		
		<?php if(empty($this->items)) { ?>
		<tr class="row0">
            <td colspan="0"><span><h2><?php echo JText::_('NO_TRIAL_BALANCE_TO_SHOW_INFO'); ?></h2></span></td>
        </tr>
		<?php } else { ?>
    <?php
    $k = 0;
	$all_credit = array();
	$all_debit = array();
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		
		
		if($row->types=="income")
		{
			$debit = "";
			$credit = $row->final_amount;
			$all_credit[] = $credit;
			$c_val = $row->final_amount;
			$d_val = 0;
		} else if($row->types=="expense") {
			$debit = $row->final_amount;
			$credit = "";
			$all_debit[] = $debit;
			$c_val = 0;
			$d_val = $row->final_amount;
		}
		
		$initial_balance = $initial_balance + $c_val - $d_val;
    ?>
        <tr class="<?php echo "row$k"; ?>">
		
            <td><?php echo $row->mode; ?></td>
            <td><?php echo $row->title; ?></td>
			
            <td><?php echo VaccountHelper::getValueFormat($debit); ?></td>
			
            <td><?php echo VaccountHelper::getValueFormat($credit); ?></td>
			<td><?php echo VaccountHelper::getValueFormat($initial_balance); ?></td>
        </tr>
    <?php
    	$k = 1 - $k;
    }
	
	//echo'<pre>';print_r($all_credit);print_r($all_debit);
	$total_debit = array_sum($all_debit);
	$total_credit = array_sum($all_credit);
    ?>
	
	<tr>
  		<td>&nbsp;</td>
		<td>&nbsp;</td>
        <td>
        	<strong><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL_DEBIT').' : '.VaccountHelper::getValueFormat($total_debit).'</span>');?></strong>
   		</td>
		<td><strong><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL_CREDIT').' : '.VaccountHelper::getValueFormat($total_credit).'</span>');?></strong></td>
	</tr>
	<?php } ?>

        <tfoot>
            <tr>
                <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
				
            </tr>
        </tfoot>
    </table>
	<div class="closing_balance">
		<div class="closing-bal-head"><?php echo JText::_('CLOSING_BALANCE'); ?></div>
		<div class="closing-bal-val"><?php echo VaccountHelper::getValueFormat($this->closingBalance); ?></div>
	</div>
	<?php } ?>
	
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="trial" />
<?php if(!$tmpl){  ?>  
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php }?>
</form>
</div>
</div>
</div>
