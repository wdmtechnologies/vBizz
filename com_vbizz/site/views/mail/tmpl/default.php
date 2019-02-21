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
JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
$session = JFactory::getSession();
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
/* if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
} */
//$mbox=$this->maillist;
$user = JFactory::getUser();
$groups = $user->getAuthorisedGroups();

$add_access = $this->config->bug_acl->get('addaccess');

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
var t_ggle=false;

jQuery(document).on('click','.unread_toggle_button',function() {
		jQuery(".main-mail-header label a").removeClass('active_head');
		jQuery(this).addClass('active_head');
		if(t_ggle==false){
			jQuery('.unread_toggle_button span').html('<?php echo JText::_("READ")?>');
			 t_ggle=true;
			 
			jQuery( ".mail-right .table tr" ).each(function() {
			// $(this).show();
			 if(jQuery(this).hasClass("read")){
			 jQuery(this).hide();
			 }
			});
			 
		}
		else if(t_ggle==true){
			jQuery('.unread_toggle_button span').html('<?php echo JText::_("UNREAD")?>');
			 t_ggle=false;
			 
			 jQuery( ".mail-right .table tr" ).each(function() {
			 jQuery(this).show();
			 if(jQuery(this).hasClass("unread")){
			 jQuery(this).hide();
			 }
			})
		}
		else{
		jQuery( ".mail-right .table tr" ).show();
		}
		
});

		jQuery(document).on('click','.all_messages',function() {
			jQuery(".main-mail-header label a").removeClass('active_head');
			jQuery(this).addClass('active_head');
			jQuery( ".mail-right .table tr" ).each(function() {
						jQuery(this).show();
			});
		});

		/*jQuery(document).on('click','.attach_file_toggle_button',function() {
			$(".main-mail-header label a").removeClass('active_head');
			$(this).addClass('active_head');
			$( ".mail-right .table tr" ).each(function() {
			 $(this).hide();
			 if($(this).hasClass("attach")){
			 $(this).show();
			 }
			});
		});*/
		
		jQuery(document).on('click','.attach_file_toggle_button',function() {
				jQuery(".main-mail-header label a").removeClass('active_head');
				jQuery(this).addClass('active_head');
				jQuery( ".mail-right .table tr" ).each(function() {
				 jQuery(this).hide();
				 if(jQuery(this).hasClass("attach")){
				 jQuery(this).show();
				 }
				});
		});
		
		jQuery(document).on('click','.delete_mail',function() {
			
			if(jQuery('input[name="cid[]"]:checked').length<=0){
				alert('<?php echo JText::_('FIRST_MAKE_SELECTION');?>');
				return false;
			}else{
				Joomla.submitform('delete_mails',document.getElementById('adminForm'));
			}
			
		});
		
		jQuery(document).on('click','.move_trash',function() {
			
			if(jQuery('input[name="cid[]"]:checked').length<=0){
				alert('<?php echo JText::_('FIRST_MAKE_SELECTION');?>');
				return false;
			}else{
				Joomla.submitform('move_trash',document.getElementById('adminForm'));
			}
			
		});

		/* jQuery(document).on('change','#mail_action',function(){	
			var action_value=jQuery(this).val();
			alert(action_value);
			jQuery(this).addClass('active_head');
			
			jQuery('#action_value').val(action_value);
			jQuery(".main-mail-header label a").removeClass('active_head');
			if(action_value==1){
				jQuery(this).attr("selected","selected");
				jQuery( ".mail-right .table tr:not(.pagination)" ).each(function() {
					jQuery(this).hide();
						if(jQuery(this).hasClass("unread")){
						jQuery(this).show();
					}
				});
			}else if(action_value==2){
				jQuery(this).attr("selected","selected");
				jQuery( ".mail-right .table tr:not(.pagination)" ).each(function() {
					jQuery(this).hide();
						if(jQuery(this).hasClass("attach")){
						jQuery(this).show();
					}
				});	
				
			}else if(action_value==3){
				jQuery(this).attr("selected","selected");
				jQuery( ".mail-right .table tr:not(.pagination)" ).each(function() {
					jQuery(this).hide();
						if(jQuery(this).hasClass("archive")){
						jQuery(this).show();
					}
				});	
				
			}else if(action_value==4){
				
			}else{
				jQuery('.all_messages').addClass('active_head');
				jQuery( ".mail-right .table tr" ).each(function() {
						jQuery(this).show();
				});
				
			}


			
		}); */
		
		
		jQuery(document).on('click','.move_archive',function() {
			
			if(jQuery('input[name="cid[]"]:checked').length<=0){
				alert('<?php echo JText::_('FIRST_MAKE_SELECTION');?>');
				return false;
			} else
			{
				Joomla.submitform('move_archive',document.getElementById('adminForm'));
			}
		});
		
 
		t_ggle_archive=false;
		
		jQuery(document).on('click','.archive_mail',function() {
		jQuery(".main-mail-header label a").removeClass('active_head');
		jQuery(this).addClass('active_head');
		if(t_ggle_archive==false){
			jQuery('.archive_mail span'	).html('<?php echo JText::_("UnArchive")?>');
			 t_ggle_archive=true;
			jQuery( ".mail-right .table tr" ).each(function() {
				jQuery(this).hide();
				if(jQuery(this).hasClass("archive")){
				jQuery(this).show();
				}
			});
			 
		}
		else if(t_ggle_archive==true){
			jQuery('.archive_mail span').html('<?php echo JText::_("Archive")?>');
			 t_ggle_archive=false;
			 
			 jQuery( ".mail-right .table tr" ).each(function() {
			 jQuery(this).hide();
			 if(jQuery(this).hasClass("no-archive")){
			 jQuery(this).show();
			 }
			})
		}

		});
		
		jQuery(document).on('click','.get_new_message',function() {
 
		  jQuery.ajax( 
			{
				url: "",
				type: "POST",
				dataType:"json",
				data: {'option':'com_vbizz', 'view':'mail', 'task':'new_mail_fetch', 'tmpl':'component',"<?php echo JSession::getFormToken(); ?>":1},

				 beforeSend: function()	{
					jQuery(".loading").css('display','inline-block');
				  },
				  complete: function()	{
					jQuery(".loading").hide();
				  },

				success: function(data)	
				{
				if(data.result){
					var html='<div class=" alert alert-success" style="top:0 !important;">';
					html +='<a data-dismiss="alert" class="close">×</a>'
					html +='<div><p>'+data.result+'</p></div></div>';
					jQuery("#system-message").html(html);
				}
				else
				alert(data.error);

			}

			});
		});
});

</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_("VACCOUNT_MAIL"); ?></h1>
	</div>
</header>
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=mail');?>" method="post" name="adminForm" id="adminForm" >

<div class="content_part">
		 <div class="loading"><div class="loading-icon"><div></div></div></div>
		<div id="system-message"></div>
		<div class="main-mail-header">
		<label class="header_unread min_header_bar min_header_full">
			<div class="vc_search">
				
				<label class="min_header_bar min_header_bar_setting">			
					<select name="action_value" class="action_value mail_action" onchange="this.form.submit();">
						<option value="0"><?php echo JText::_('SELECT_ACTIONS');?></option>
						<option value="1" <?php if($this->lists['action_value']==1) echo "selected='selected'";?>><i class="fa fa-adjust"></i><?php echo JText::_('UNREAD');?></option>
						<option value="2" <?php if($this->lists['action_value']==2) echo "selected='selected'";?>><i class="fa fa-file-text"></i><?php echo JText::_('ATTACHMENT');?></option>
						<option value="3" <?php if($this->lists['action_value']==3) echo "selected='selected'";?>><i class="fa fa-archive"></i><?php echo JText::_('ARCHIVE');?></option>
						<option value="4" <?php if($this->lists['action_value']==4) echo "selected='selected'";?>><i class="fa fa-trash"></i><?php echo JText::_('TRASH');?></option>
					</select>
					
				</label>	
				
				<label class="header_unread min_header_bar">
					<input type="text" class="text_area ui-autocomplete-input" value="<?php echo $this->lists['search'];?>" placeholder="<?php echo JText::_('SERACH'); ?>" id="search" name="search" autocomplete="off">
					<button class="btn" id="" onclick="this.form.submit();"><i class="fa fa-search"></i></button>

					<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>	
			  </label>
			  
			</div>
			<div class="vc_search">		
				
				<label class="header_unread min_header_bar">
					<a class="get_new_message" href="javascript:void(0)">
					<span><?php echo JText::_("GET_MESSAGE");?></span>
					<p  class="icon_p"><i class="fa fa-refresh"></i></p>
					</a>
				</label>
				
				<label class="header_unread min_header_bar">
					<a class="all_messages active_head" href="javascript:void(0)">
					<span><?php echo JText::_("INBOX");
					echo ' ('.count($this->items).') ';
					?></span>
					<p class="icon_p"><i class="fa fa-envelope"></i></p>
					</a>
				</label>
				
				<label class="min_header_bar">			
					<a class="modal" id="" title="Composed mail" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=mail&layout=modal&type_mail=0&tmpl=component';?>'" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
					 <span><?php echo JText::_("COMPOSED")?></span>
					 <p class="icon_p"><i class="fa fa-envelope-o"></i></p>
					 </a>
				</label>
			
				<label class="header_unread min_header_bar">
					<a class="move_trash" href="javascript:void(0)">
					<span><?php echo JText::_("MOVE_TO_TRASH");?></span>
					<p class="icon_p"><i class="fa fa-trash-o"></i></p>
					</a>
				</label>
								
				<label class="header_unread min_header_bar">
					<a class="move_archive" href="javascript:void(0)">
					<span><?php echo JText::_("MOVE_ARCHIVE")?></span>
					<p class="icon_p"><i class="fa fa-arrows"></i></p>
					</a>
				</label>	
				
				
				<label class="min_header_bar min_header_bar_setting">
					<a class="modal configuartion_link" id="modal" title="Mail Configuration" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=mail&layout=configuration&tmpl=component';?>'" rel="{handler: 'iframe', size: {x: 700, y: 600}}">
					<span><?php echo JText::_("CONFIGURATION");?></span>
					<p class="icon_p"><i class="fa fa-cog"></i></p></a>
				</label>
								
			</div>

			<?php /*

			<label class="header_unread min_header_bar">
			<a class="delete_mail" href="javascript:void(0)">
			<span><?php echo JText::_("DELETE");?></span>
			<p class="icon_p"><i class="fa fa-trash-o"></i></p>
			</a>
			</label>
			
			
			<label class="header_unread min_header_bar">
			<a class="archive_mail" href="javascript:void(0)">
			<span><?php echo JText::_("ARCHIVE")?></span>
			<p class="icon_p"><i class="fa fa-folder-o"></i></p>
			</a></label>	
			
			<label class="header_unread min_header_bar">
			<a class="unread_toggle_button" href="javascript:void(0)">
			<span><?php echo JText::_("UNREAD")?></span>
			<p class="icon_p"><i class="fa fa-envelope-o"></i></p>
			</a></label>			
			<label class="header_unread min_header_bar">
			<a class="attach_file_toggle_button" href="javascript:void(0)">
			<span><?php echo JText::_("ATTACHMENT")?></span>
			<p class="icon_p"><i class="fa fa-envelope-o"></i></p>
			</a></label>

			</label>
		    </label>
			*/?>
		</div>
		<div class="mail-right">
		<table class="adminlist table">
			<thead>
				<tr>
					<th width="10" class="hidden-phone"><?php echo JHTML::_('grid.sort', 'SR_NO', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
					<th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
					<th><?php echo JHTML::_('grid.sort', 'SUBJECT', 'i.subject', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
					<th><?php echo JHTML::_('grid.sort', 'FROM', 'i.from', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
					<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DATE', 'i.mail_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
					<?php if($addaccess) { ?><th class="hidden-phone"></th><?php } ?>
					
				</tr>
			</thead>
			<?php
			$k = 0;	
 
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
			
			$row = &$this->items[$i];
			$canEdit = $user->authorise('core.edit', 'com_vbizz');
			$canChange = $user->authorise('core.edit.state', 'com_vbizz');
			$checked 	= JHTML::_('grid.id',   $i, $row->id );
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=mail&task=edit&cid[]='. $row->id );
			$readUnread=$row->seen==1?'read':'unread';
			$attachefile =$row->attachments==1?'attach':'no-attach';
			$archive_mail =$row->archive_mail==1?'archive':'no-archive';
			$trash_mail =$row->published==0?'trash':'no-trash';	
			

			?>
			
				<tr class="<?php echo $readUnread.' '.$attachefile.' '.$archive_mail.' '.$trash_mail ?>" <?php if($readUnread=='unread') echo 'style="font-weight: bold;"' ?>>
			
					<td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
				
					<td><?php echo $checked;?></td>
										
					<td align="center">
						<a href="<?php echo $link; ?>"><?php echo substr($row->subject,0,50); 
						?></a>
					</td>
					
					<td align="center"><?php echo $row->from_name?$row->from_name:$row->from_email; ?></td>
					
				   <td align="center" class="hidden-phone"><?php echo $row->mail_date; ?></td>
				   
				   <?php if($addaccess) { ?>
				   <td align="center" class="hidden-phone" id="bug-icon-<?php echo $row->id; ?>">
				   <?php if($row->bug) { ?>
						<span><i class="fa fa-star"></i></span>
				   <?php } else { ?>
				   <a class="modal" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=mail&layout=bugnotes&tmpl=component&edit=0&id='.$row->id;?>" rel="{handler: 'iframe', size: {x: 600, y: 300}}">
					<span class="hasTip" title="<?php echo JText::_('MOVE_TO_BUG_TRACKER'); ?>"><i class="fa fa-arrows"></i></span>
					</a>
				   <?php } ?>
					</td>
				   <?php } ?>
				</tr>
			<?php
				$k = 1 - $k;
		}	
			?>
			<tfoot>
				<tr class="pagination">
					<td colspan="9"><?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
		
		</div>
	</div>
 		
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="mail_password" value="0" />
<input type="hidden" name="view" value="mail" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
 </div>
 </div>