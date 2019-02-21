<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user = JFactory::getUser();
?>

<script>
function doConfirm(msg, yesFn, noFn) {
    var confirmBox = jQuery("#confirmBox");
    confirmBox.find(".message").text(msg);
    confirmBox.find(".yes,.no").unbind().click(function () {
        confirmBox.hide("slow" );
    });
    confirmBox.find(".yes").click(yesFn);
    confirmBox.find(".no").click(noFn);
    confirmBox.show("slow" );
}




function checkstatus(e) {
  
   if(jQuery(e).hasClass("disabled"))
    return false;
  
    jQuery.noConflict();
	var email = jQuery('input[name="emailaddress"]').val();
     var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!re.test(email))
	{
			document.adminForm.emailaddress.focus(); 
			alert("<?php echo JText::_('COM_VBIZZ_PLZ_ENTER_CORRECT_EMAIL');?>");	
	  return false;	
	}
	if(jQuery('input[name="password"]').val()==''){
			document.adminForm.password.focus(); 
			alert("<?php echo JText::_('COM_VBIZZ_PLZ_ENTER_PASSWORD');?>");
	  return false;	
	}
	 jQuery(e).addClass("disabled");
    var jqxhr = jQuery.ajax({
        type: "POST",
		dataType: "json",
        url: "<?php echo JRoute::_('index.php?option=com_vbizz'); ?>",
        data: { "task":"checkstatus", "password": jQuery('input[name="password"]').val(),"emailaddress": jQuery('input[name="emailaddress"]').val(), "<?php echo JSession::getFormToken(); ?>":1, 'abase':1, 'tmpl':'component'},
		beforeSend: function()	{ 
		jQuery('.vbizz_overlay').show();	
		},
		complete: function()	{
		jQuery('.vbizz_overlay').hide();	
		},
		success: function(res)	{ 
		               jQuery(e).removeClass("disabled");
						if(res.result == "success")
						{  
							if(res.status=='ok')
							{
							  jQuery(e).removeClass("disabled");
							  window.location.replace("<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz', false);?>"); 
							} 
							
                            if(res.status=='No')
							{
							  if(res.msg)
							  {
						    doConfirm(res.msg+'\n <?php echo JText::_('COM_VBIZZ_PLEASE_PURCHASE_SUBSCRIPTION_EXTRA');?>', function yes() {
                            window.location.href='https://www.wdmtech.com/vbizz';	
                              }, function no() {
                           // do nothing
                            });
								    
							  }
							else
							{   
							doConfirm('<?php echo JText::_('COM_VBIZZ_PLEASE_PURCHASE_SUBSCRIPTION');?>\n'+'\n <?php echo JText::_('COM_VBIZZ_PLEASE_PURCHASE_SUBSCRIPTION_EXTRA');?>', function yes() {
                            window.location.href='https://www.wdmtech.com/vbizz';	
                              }, function no() {
                           // do nothing
                            });
							}
									
							}							
						}
					},
					error: function(jqXHR, textStatus, errorThrown)	{
					jQuery(e).removeClass("disabled");	
						window.location.href= '<?php echo JRoute::_('index.php?option=com_vbizz&view=vbizz',false);?>';
					}
      });
}



</script>
		<div class="vbizz_overlay" style="display:none;"> 
		<img class="vbizz-loading" src="<?php echo JURI::root();?>components/com_vbizz/assets/images/loading_second.gif" alt="">
		</div>
        <div class="login_block_box">
            <form action="index.php?option=com_vbizz&view=vbizz" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
			<div class="login_box_logo">
			<img src="<?php echo JUri::root().'administrator/components/com_vbizz/assets/images/vbizz-logo.png';?>">
			<h3><?php echo JText::_( 'COM_VBIZZ_SUBSCRIPTION_TITLE' ); ?></h3>
			</div>
			<div class="login_block_box_inner">
			<div class="login_box_title"><?php echo JText::_( 'COM_VBIZZ_SUBSCRIPTION_INFO' ); ?></div>
               <div class="login_box_fields">
			   <div class="control-group">
					<div class="controls">
						<input class="inputbox" type="text" placeholder="<?php echo JText::_( 'COM_VBIZZ_REGISTER_EMAIL_ADDRESS' ); ?>" name="emailaddress" id="emailaddress" size="60" value="" />
					</div>
		       </div>
			   <div class="control-group">
					<div class="controls">
						<input class="inputbox" type="password" placeholder="<?php echo JText::_( 'COM_VBIZZ_REGISTER_PASSWORD' ); ?>" name="password" id="password" size="60" value="" />
					</div>
				</div>
                 <div class="control-group control-submit">
                   <div class="controls"> 
                        <div class="btn" onclick="checkstatus(this);"><?php echo JText::_('COM_VBIZZ_LOGINSUBMIT'); ?></div>
					</div>
               
              </div>
			  </div>
			    <div class="note_text"><?php echo JText::_('COM_VBIZZ_SUBSCRIPTION_NOTE'); ?></div>
			  </div>
            </form>
        </div>
		<div id="confirmBox">
		        <div class="confirmBoxinner">
				<div class="message"></div>
				<span class="button yes btn btn-success"><?php echo JText::_('COM_VBIZZ_REDIRECT'); ?></span>
				<span class="button no btn btn-danger"><?php echo JText::_('COM_VBIZZ_CANCEL'); ?></span>
				</div>
        </div>
    <?php

