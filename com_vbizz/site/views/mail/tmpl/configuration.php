<?php

/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
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
$document = JFactory::getDocument();
?>
<style>
.alert{
top:0 !important;
}
</style>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<form action="index.php?option=com_vbizz&view=mail&layout=configaration&tmpl=component" method="post" name="adminForm" id="adminForm">

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <?php echo JText::_('SMTP_SERVER_OUTGOING_MSG')?>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
 
				<table class="adminform table table-striped">
					<tbody><tr>
						<th><label class="required"><?php echo JText::_('FROM_EMAIL');?></label></th>
						<td><input type="text" size="50" name="smtp_email" value="<?php echo $user->email;?>" class="inputbox required" id="smtp_from_mail" ></td>
					</tr>
					
					<tr>
						<th><label class="required"><?php echo JText::_('FROM_NAME');?></label></th>
						<td><input type="text" size="50" value="<?php echo $user->name;?>" class="inputbox required" id="smtp_username" name="smtp_username"></td>
					</tr>
					
					<tr>
						<th><label class="required"><?php echo JText::_('SMTP_AUTHENTICATION');?></label></th>
						<td>
							<label class="radio-inline"><?php echo JText::_('YS');?></label>
							  <input type="radio" name="smtp_authentication" id="smtp_authentication1" value="1" <?php if(isset($this->mailsetting->smtp_authentication) && $this->mailsetting->smtp_authentication==1) echo 'checked="checked"'?>>
							
							<label class="radio-inline"><?php echo JText::_('NOS');?></label>
							  <input type="radio" name="smtp_authentication" id="smtp_authentication2" value="0" <?php if(isset($this->mailsetting->smtp_authentication) && $this->mailsetting->smtp_authentication==0) echo 'checked="checked"'?>>
							
						</td>
					</tr>
					
					<tr>
						<th><label class="required"><?php echo JText::_('SMTP_SECURITY');?></label></th>
						<td>
							<select name="smtp_security" id="smtp_security" style="width:200px;">
							<option  value="none"><?php echo JText::_('NONE');?></option>
							<option value="ssl" <?php if(isset($this->mailsetting->smtp_security) && $this->mailsetting->smtp_security=='ssl') echo 'selected="selected"'?>><?php echo JText::_('SSL');?></option>
							<option value="tls" <?php if(isset($this->mailsetting->smtp_security) && $this->mailsetting->smtp_security=='tls') echo 'selected="selected"'?>><?php echo JText::_('TLS');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('SMTP_PORT');?></label></th>
						<td>
						<input type="text" name="smtp_port" value="<?php echo !empty($this->mailsetting->smtp_port)?$this->mailsetting->smtp_port:25; ?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('SMTP_PASSWORD');?></label></th>
						<td>
						<input type="password" name="smtp_password" value="<?php echo !empty($this->mailsetting->smtp_password)?$this->mailsetting->smtp_password:'';?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('CONFIRM_PASSWORD');?></label></th>
						<td><input type="password" rows="5" cols="39" value="<?php echo !empty($this->mailsetting->smtp_password)?$this->mailsetting->smtp_password:'';?>" autocomplete="off" name="smtp_password2" class="inputbox"></td>
					</tr>
					
					<tr>
						<th><label class="required"><?php echo JText::_('SMTP_HOST');?></label></th>
						<td><input type="text" rows="5" cols="39" value="<?php echo !empty($this->mailsetting->smtp_host)?$this->mailsetting->smtp_host:'';?>" autocomplete="off" name="smtp_host" class="inputbox"></td>
					</tr>
				</tbody></table>
      </div>
    </div>
  </div>
  
  
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          <?php echo JText::_('IMAP_SERVER_INCOMNG_MSG');?>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
		<table class="adminform table table-striped">
					<tbody> 
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_EMAIL');?></label></th>
						<td>
						<input type="text" name="imap_email" value="<?php echo $user->email;?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_NAME');?></label></th>
						<td>
						<input type="text" name="imap_username" value="<?php echo $user->name;?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_PASSWORD');?></label></th>
						<td>
						<input type="password" name="imap_password" value="<?php echo !empty($this->mailsetting->imap_password)?$this->mailsetting->imap_password:''; ?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_CONFIRM_PASSWORD');?></label></th>
						<td><input type="password" rows="5" cols="39" value="<?php echo !empty($this->mailsetting->imap_password)?$this->mailsetting->imap_password:''; ?>" autocomplete="off" name="imap_password2" class="inputbox"></td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_PORT');?></label></th>
						<td>
						<input type="text" name="imap_port" value="<?php echo !empty($this->mailsetting->imap_port)?$this->mailsetting->imap_port:143; ?>">
						</td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_HOST');?></label></th>
						<td><input type="text" rows="5" cols="39" value="<?php echo !empty($this->mailsetting->imap_host)?$this->mailsetting->imap_host:''; ?>" autocomplete="off" name="imap_host" class="inputbox"></td>
					</tr>
					<tr>
						<th><label class="required"><?php echo JText::_('IMAP_SECURITY');?></label></th>
						<td>
							<select name="imap_security" id="">
								<option <?php if(isset($this->mailsetting->imap_security) && $this->mailsetting->imap_security=='notls') echo 'selected="selected"';?> value="notls"><?php echo JText::_('notls');?></option>
								<option value="tls" <?php if(isset($this->mailsetting->imap_security) && $this->mailsetting->imap_security=='tls') echo 'selected="selected"'?>><?php echo JText::_('TLS') ?></option>
								<option value="ssl" <?php if(isset($this->mailsetting->imap_security) && $this->mailsetting->imap_security=='ssl') echo 'selected="selected"'?>><?php echo JText::_('SSL');?></option>
							</select>
						</td>
					</tr>

				</tbody></table>
				<div>
						<label>
						<?php echo JText::_("GOOGLE_SERVER");?>
						</label>
						<label>
						<?php echo JText::_("GOOGLE_SERVER_NOTICE");?>
						<a target="_blank" href="https://support.google.com/accounts/answer/6010255?hl=en">
						https://support.google.com/accounts/answer/6010255?hl=en</a>					
						</label>
						
				</div> 
      </div>
    </div>
  </div>
   			<div class="cog_button">
				<button type="button" onclick="Joomla.submitform('imap_setting', document.getElementById('adminForm'));" class="btn btn-lg btn-block"><?php echo JText::_('SAVE');?></button>
			</div>
</div>



<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="mail" />
</form>