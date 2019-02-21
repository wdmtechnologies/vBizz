<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$id = JRequest::getInt('id', 0);


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add access
$add_access = $this->config->import_acl->get('addaccess');

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
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('IMPORT_EXPORT_DATA'); ?></h1>
	</div>
</header>
<div class="content_part">

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=import'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    <?php if($addaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-new">
                        <span onclick="Joomla.submitbutton('importready')" class="btn btn-small btn-success">
                        <span class="fa fa-arrow-circle-right"></span> <?php echo JText::_('CONTINUE'); ?></span>
                    </div>
                    <?php } ?>
                    
                    <div class="btn-wrapper"  id="toolbar-cancel">
                        <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                        <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col100">
<fieldset class="adminform">
<legend style="border: medium none; margin: 0px 0px 5px;"><?php echo JText::_('IMPORT'); ?></legend>
<table class="adminform table table-striped">
<tbody>

	<tr>
        <th width="200">
        	<label class="hasTip" title="<?php echo JText::_('IMPORTTYPETXT'); ?>"><?php echo JText::_('SELECT_IMPORT_TYPE'); ?></label>
        </th>
        <td>
        	<select name="import_type" id="import_type">
            <option value=""><?php echo JText::_('SELECT_FORMAT'); ?></option>
            <option value=".csv"><?php echo JText::_('CSV'); ?></option>
            <option value=".json"><?php echo JText::_('JSON'); ?></option>
            <option value=".xml"><?php echo JText::_('XML'); ?></option>
        	</select>
        </td>
	</tr>
    
    <tr>
        <th width="200">
        	<label class="hasTip" title="<?php echo JText::_('FILETXT'); ?>"><?php echo JText::_('FILE'); ?></label>
        </th>
        <td>
        	<input type="file" name="file" id="file" class="inputbox required" size="50" accept="application/csv" />
        </td>
	</tr>
    
    <tr>
    	<th width="200">
        	<label class="hasTip" title="<?php echo JText::_('URLUPLOADTXT'); ?>"><?php echo JText::_('UPLOAD_BY_URL'); ?></label>
        </th>
        <td>
        	<input type="text" name="url_file" id="url_file" value="" />
        </td>
    </tr>
</tbody>
</table>
</fieldset>

</div>

<?php if($addaccess) { ?> 
<div>
<fieldset class="adminform">
<legend style="border: medium none; margin: 0px 0px 5px;"><?php echo JText::_('EXPORT'); ?></legend>
<table class="adminform table table-striped">
<tbody>

    <tr>
    	<th><label class="hasTip" title="<?php echo JText::_('INCOMEEXPTXT'); ?>"><?php echo JText::_('INCOME'); ?></label></th>
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=income&task=export&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('CSV'); ?></a>
            </div>
        </td>
        
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=income&task=jsonExport&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('JSON'); ?></a>
            </div>
        </td>
        
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=income&task=xmlExport&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('XML'); ?></a>
            </div>
        </td>
    </tr>
    
    <tr>
    	<th><label class="hasTip" title="<?php echo JText::_('EXPENSEEXPTXT'); ?>"><?php echo JText::_('EXPENSE'); ?></label></th>
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=expense&task=export&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('CSV'); ?></a>
            </div>
        </td>
        
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=expense&task=jsonExport&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('JSON'); ?></a>
            </div>
        </td>
        
        <td>
            <div class="btn-wrapper"  id="toolbar-export">
            <a href="index.php?option=com_vbizz&view=expense&task=xmlExport&tmpl=component" class="btn btn-small">
            <span class="fa fa-upload"></span> <?php echo JText::_('XML'); ?></a>
            </div>
        </td>
    </tr>
</tbody>
</table>
</fieldset>    
<?php } ?>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="import" />
</form>

</div>
</div>
</div>
</div>