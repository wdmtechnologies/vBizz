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
 ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			var form = document.adminForm;
		
			if(form.state_name.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_STATE_NAME'); ?>");
				return false;
			}
			if(form.country_id.value == "")	{
				alert("<?php echo JText::_('SELECT_COUNTRY'); ?>");
				return false;
			}
			
			if(typeof(validateit) == 'function')	{
				if(!validateit())
					return false;
			}
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
	</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  <div class="col100">
    <fieldset class="adminform">
      <legend><?php if($this->states->id) echo JText::_('EDIT_RECORD'); else echo JText::_('ADD_NEW');?></legend>
      <table class="adminform table table-striped">
        <tbody>
          <tr class="admintable">
            <td width="200"><label class="hasTip" title="<?php echo JText::_('STATENAMETXT'); ?>">
				<?php echo JText::_('STATE_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </td>
            <td>
            	<input class="text_area" type="text" name="state_name" id="state_name" size="32" maxlength="50" value="<?php echo $this->states->state_name;?>"/>
            </td>
          </tr>
          
          <tr>
            <td><label class="hasTip" title="<?php echo JText::_('STATECOUNTRYTXT'); ?>">
            <?php echo JText::_('COUNTRY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></td>
            <td>
                <select name="country_id" id="country_id">
                <option value=""><?php echo JText::_('SELECT_COUNTRY'); ?></option>
                <?php	for($i=0;$i<count($this->countries);$i++)	{	?>
                <option value="<?php echo $this->countries[$i]->id; ?>" <?php if($this->countries[$i]->id==$this->states->country_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->countries[$i]->country_name); ?></option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        
        <tr>
            <td><label class="hasTip" title="<?php echo JText::_('STATUSTXT'); ?>"><?php echo JText::_('STATUS'); ?></label></td>
            <td>
				<fieldset class="radio btn-group" style="margin-bottom:9px;">
					<label for="published1" id="published-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
					<input type="radio" name="published" id="published1" value="1" <?php if($this->states->published) echo 'checked="checked"';?>/>
					<label for="published0" id="published-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
					<input type="radio" name="published" id="published0" value="0" <?php if(!$this->states->published) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
        </tr>
           
        </tbody>
      </table>
    </fieldset>
  </div>
  <div class="clr"></div>
  <input type="hidden" name="option" value="com_vbizz" />
  <input type="hidden" name="id" value="<?php echo $this->states->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="view" value="states" />
</form>