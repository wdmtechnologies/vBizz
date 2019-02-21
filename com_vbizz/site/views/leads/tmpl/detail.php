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
defined( '_JEXEC' ) or die( 'Restricted access' ); 
 $ot = JRequest::getInt('ot',0);
JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
 if($ot) { ?>

 
  <script type="text/javascript">
  var check_object = '';   
  var sid = 0; 
  var leadsourceid = 0;  
  var leadsourcename = ''; 
  var action_name = ''; 
  var section_name = '';   
  function edite_lead_source(e){ 
		    check_object = parseInt(jQuery(e).parents('tr').index());
		    leadsourceid = jQuery(e).attr('data-leadid');
		    leadsourcename = jQuery(e).attr('data-leadname');
			section_name = jQuery(e).attr('data-action');
			action_name = jQuery(e).attr('data-for');
		     sid = jQuery(e).attr('data-sn');  
		var old_html =  jQuery(e).parents('tr').html();
		var new_html ='<td></td><td><input type="" name="leadsourcename" value="'+leadsourcename+'"></td><td><span class="btn btn-success save_leadsource"><?php echo JText::_("COM_VBIZZ_LEADS_SAVE");?></span><span class="btn btn-cancel cancel_leadsource"><?php echo JText::_("COM_VBIZZ_LEADS_CANCEL");?></span></td>';    
	    
		jQuery(e).parents('tr').html(new_html);
	   
		jQuery('.cancel_leadsource').on('click', function()
		{ 
		jQuery('table.'+jQuery(this).parents('table').attr('data-tablename')+' tr').eq(check_object+1).html(old_html);	
		//jQuery(check_object).parents('tr').html(old_html);	
		});
	
		jQuery('.save_leadsource').on('click', function()
		{
			update_lead_source(jQuery(this).parents('tr'));
		}); 
       }
	  function add_lead_source(e)
	  { 
		section_name = jQuery(e).attr('data-action');
		action_name = jQuery(e).attr('data-for');
		check_object = parseInt(jQuery('table.'+section_name+' tr').length);
		leadsourceid = '';
		sid = check_object;
		check_object = check_object-1;
        var new_html ='<tr class="remove_tr"><td></td><td><input type="" name="leadsourcename" value=""></td><td><span class="btn btn-success save_leadsource_add"><?php echo JText::_("COM_VBIZZ_LEADS_SAVE");?></span><span class="btn btn-cancel cancel_leadsource_add"><?php echo JText::_("COM_VBIZZ_LEADS_CANCEL");?></span></td></tr>'; 
		//alert(section_name);
		jQuery('table.'+section_name).append(new_html);
         jQuery('.cancel_leadsource_add').on('click', function()
		{ 
		jQuery('tr.remove_tr').remove();	
		
		});	
        jQuery('.save_leadsource_add').on('click', function()
		{  
		
		update_lead_source(jQuery(this).parents('tr'));
		}); 		
	  }  
		function update_lead_source(r)
		{ 	
			
			leadstatus = r.find('input').val();
			
			jQuery.ajax({
				type: "POST",
				dataType:"JSON",
				data: {'option':'com_vbizz', 'view':'leads', 'task':action_name, 'tmpl':'component','sourcename':leadstatus, 'leadsourceid':leadsourceid },
				
				beforeSend: function() {
					jQuery("span."+section_name).show();
				},
				
				complete: function()      {
					jQuery("span."+section_name).hide();
				},

				success: function(data){
					if(data.result=='success')
					{  
					new_html = '<td>'+sid+'</td><td>'+leadstatus+'</td><td><span class="edit btn"><a href="javascript:void(0);" data-action="'+section_name+'" data-for="'+action_name+'" data-sn="'+sid+'" data-leadname="'+leadstatus+'" data-leadid="'+data.leadsourceid+'" onclick="edite_lead_source(this);"><?php echo JText::_("EDIT");?></a></span><span class="delete btn"><a href="javascript:void(0);" data-sn="'+sid+'" data-action="'+section_name+'" data-for="'+action_name+'" data-leadname="'+leadstatus+'" data-leadid="'+data.leadsourceid+'" onclick="delete_lead_source(this);"><?php echo JText::_("DELETE");?></a></span></td>';
					jQuery('table.'+section_name+' tr').eq(check_object+1).html(new_html); 
					if(section_name=='updatinglead1') var select_filter = 'lead_status';
					if(section_name=='updatinglead2') var select_filter = 'lead_industry';
					if(section_name=='updatinglead3') var select_filter = 'lead_source';
					
                    window.parent.jQuery('select#'+select_filter).append(data.leadsourcelist);
			        window.parent.jQuery('select#'+select_filter).trigger('liszt:updated');					
					
					}
				}
			});
		}
      
       function delete_lead_source(r)
		{ 	
			section_name = jQuery(r).attr('data-action');
		    action_name = jQuery(r).attr('data-for');
			leadsourceid = jQuery(r).attr('data-leadid');
		    leadsourcename = jQuery(r).attr('data-leadname');
			
			jQuery.ajax({
				type: "POST",
				dataType:"JSON",
				data: {'option':'com_vbizz', 'view':'leads', 'task':'delete_leads', 'tmpl':'component', 'action_from':action_name, 'leadsourceid':leadsourceid },
				
				beforeSend: function() {
					jQuery("span."+section_name).show();
				},
				
				complete: function()      {    
					jQuery("span."+section_name).hide();
				},

				success: function(data){
					if(data.result=='success')
					{  
					
					jQuery(r).parents('tr').remove();
                    if(section_name=='updatinglead1') var select_filter = 'lead_status';
					if(section_name=='updatinglead2') var select_filter = 'lead_industry';
					if(section_name=='updatinglead3') var select_filter = 'lead_source';
					window.parent.jQuery('select#'+select_filter).empty();
                    window.parent.jQuery('select#'+select_filter).append(data.leadsourcelist);
			        window.parent.jQuery('select#'+select_filter).trigger('liszt:updated');
					}
				}
			});
		}
jQuery(function($) {
			 $('.hasTip').each(function() {
				var title = $(this).attr('title');
				if (title) {
					var parts = title.split('::', 2);
					$(this).data('tip:title', parts[0]);
					$(this).data('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($('.hasTip').get(), {"maxTitleChars": 50,"fixed": false});
		});
		jQuery(function($) {
			SqueezeBox.initialize({});
			SqueezeBox.assign($('a.modal').get(), {
				parse: 'rel'
			});
		});

				jQuery(document).ready(function (){
					jQuery('select').chosen({"disable_search_threshold":10,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				});
			
window.setInterval(function(){var r;try{r=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP")}catch(e){}if(r){r.open("GET","./",true);r.send(null)}},3540000);
  </script>
  
<?php } ?>
<script type="text/javascript">
jQuery(function() {
	
	/* jQuery(document).on('click','#save-config',function() {
		
		
		var leadstatus = jQuery('input[name="leadstatus"]').val();
		
		
		var that=this;

		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz', 'view':'leads', 'task':'updateLeadStatus', 'tmpl':'component','leadstatus':leadstatus, 'ot':<?php echo $ot; ?> },
			
			beforeSend: function() {
				jQuery(that).find("span.loadingbox").show();
			},
			
			complete: function()      {
				jQuery(that).find("span.loadingbox").hide();
			},

			success: function(data){
				jQuery('.alert-msg').text(data.msg);
			}
		});
	}); */
	
});

</script>

<div id="editcell">    
   <div class="lead_span3">
    <table class="adminlist table updatinglead1" data-tablename="updatinglead1">
        <thead>
            <tr>
             <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			 <th class="hidden-phone"><?php echo JText::_('COM_VBIZZ_LEADS_STATUS')?></th>
		     <th class="hidden-phone" width="163"><?php echo JText::_('COM_VBIZZ_LEADS_ACTION')?></th>
		    </tr>
			</thead>
		   <?php 
		      $g = 0;
	 
              for ($l=0, $n=count( $this->lead_status ); $l < $n; $l++)
			   {
					$lead_source = $this->lead_status[$l];
					?>
					<tr>  
					   <td><?php echo $l+1;?></td>
					    <td><?php echo JText::_($lead_source->source_name);?></td>
					    <td width="163">
						<?php echo '<span class="edit btn"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-for="updateLeadStatus" data-action="updatinglead1" data-leadname="'.JText::_($lead_source->source_name).'" data-leadid="'.$lead_source->source_id.'" onclick="edite_lead_source(this);"><i class="fa fa-edit"></i> '.JText::_("EDIT").'</a></span>';
						echo '<span class="delete btn btn-danger"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-for="updateLeadStatus" data-action="updatinglead1" data-leadname="'.JText::_($lead_source->source_name).'" data-leadid="'.$lead_source->source_id.'" onclick="delete_lead_source(this);"><i class="fa fa-remove"></i> '.JText::_("DELETE").'</a></span>'?>
						
						</td> 
					
					</tr>
					
					<?php $g = 1 - $g;
			  
			  }
		?>
		</table><table class="adminlist table">
		<tr>
			<td>
			
				<div class="config-save">
						<a id="save-config" data-action="updatinglead1" data-for="updateLeadStatus" onclick="add_lead_source(this);" href='javascript:void(0);' class="btn btn-success">
									<span class="fa fa-plus"></span> <?php echo JText::_('ADD'); ?>
									<span class="updatinglead1" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
						</a>
				
				</div>
			</td>
		
		</tr>
       
	
</table>
</div>
	<div class="lead_span3">
            <table class="adminlist table updatinglead2" data-tablename="updatinglead2">
        <thead>
            <tr>
             <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			 <th class="hidden-phone"><?php echo JText::_('COM_VBIZZ_LEAD_INDUSTRY')?></th>
		     <th class="hidden-phone" width="163"><?php echo JText::_('COM_VBIZZ_LEADS_ACTION')?></th>
		    </tr>
			</thead>
		   <?php 
		      $g = 0;
	 
              for ($l=0, $n=count( $this->lead_industry ); $l < $n; $l++)
			   {
					$lead_source = $this->lead_industry[$l];
					?>
					<tr>  
					   <td><?php echo $l+1;?></td>
					    <td><?php echo JText::_($lead_source->industry_name);?></td>
					    <td width="163">
						<?php echo '<span class="edit btn"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-action="updatinglead2" data-for="updateLeadIndustry" data-leadname="'.JText::_($lead_source->industry_name).'" data-leadid="'.$lead_source->industry_id.'" onclick="edite_lead_source(this);"><i class="fa fa-edit"></i> '.JText::_("EDIT").'</a></span><span class="delete btn btn-danger"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-action="updatinglead2" data-for="updateLeadIndustry" data-leadname="'.JText::_($lead_source->industry_name).'" data-leadid="'.$lead_source->industry_id.'" onclick="delete_lead_source(this);"><i class="fa fa-remove"></i> '.JText::_("DELETE").'</a></span>'?>
						
						</td> 
					
					</tr>
					
					<?php $g = 1 - $g;
			  
			  }
		?>
		</table>
		<table class="adminlist table">
		<tr>
			<td>
			
				<div class="config-save">
						<a id="save-config" data-action="updatinglead2" data-for="updateLeadIndustry"  onclick="add_lead_source(this,'updatinglead1');" href='javascript:void(0);' class="btn btn-success">
									<span class="fa fa-plus"></span> <?php echo JText::_('ADD'); ?>
									<span class="updatinglead2" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
						</a>
				
				</div>
			</td>
		
		</tr>
       
	
       </table>
	</div>
	<div class="lead_span3">
	 <table class="adminlist table updatinglead3" data-tablename="updatinglead3">
        <thead>
            <tr>
             <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			 <th class="hidden-phone"><?php echo JText::_('COM_VBIZZ_LEADS_SOURCE')?></th>
		     <th class="hidden-phone" width="163"><?php echo JText::_('COM_VBIZZ_LEADS_ACTION')?></th>
		    </tr>
			</thead>
		   <?php 
		      $g = 0;
	 
              for ($l=0, $n=count( $this->lead_sources ); $l < $n; $l++)
			   {
					$lead_source = $this->lead_sources[$l];
					?>
					<tr>  
					   <td><?php echo $l+1;?></td>
					    <td><?php echo JText::_($lead_source->source_name);?></td>
					    <td width="163">
						<?php echo '<span class="edit btn"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-action="updatinglead3" data-for="updateLeadSource" data-leadname="'.JText::_($lead_source->source_name).'" data-leadid="'.$lead_source->source_id.'" onclick="edite_lead_source(this);"><i class="fa fa-edit"></i> '.JText::_("EDIT").'</a></span><span class="delete btn btn-danger"><a href="javascript:void(0);" data-sn="'.($l+1).'" data-action="updatinglead3" data-for="updateLeadSource" data-leadname="'.JText::_($lead_source->source_name).'" data-leadid="'.$lead_source->source_id.'" onclick="delete_lead_source(this);"><i class="fa fa-remove"></i> '.JText::_("DELETE").'</a></span>'?>
						
						</td> 
					
					</tr>
					
					<?php $g = 1 - $g;
			  
			  }
		?>
		</table><table class="adminlist table">
		<tr>
			<td>
			
				<div class="config-save">
						<a id="save-config" data-action="updatinglead3" data-for="updateLeadSource" onclick="add_lead_source(this);" href='javascript:void(0);' class="btn btn-success">
									<span class="fa fa-plus"></span> <?php echo JText::_('ADD'); ?>
									<span class="updatinglead3" style="display:none;"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif"   />' ?></span>
						</a>
				
				</div>
			</td>
		
		</tr>
       
	
    </table>
  </div>
</div>
