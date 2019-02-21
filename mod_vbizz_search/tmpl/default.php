<?php
/*------------------------------------------------------------------------
# mod_vaccount_search - vAccount Search
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2015 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');

?>


<script>
jQuery.widget( "custom.catcomplete", jQuery.ui.autocomplete, {
	_create: function() {
		this._super();
		this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
	},
	_renderMenu: function( ul, items ) {
		var that = this,
		currentCategory = "";
		jQuery.each( items, function( index, item ) {
			var li;
			if ( item.category != currentCategory ) {
				ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
				currentCategory = item.category;
			}
			li = that._renderItemData( ul, item );
			if ( item.category ) {
				li.attr( "aria-label", item.category + " : " + item.label );
			}
		});
	}
});
</script>
  
<script>
jQuery(function() {
	
	jQuery("#vc_search").catcomplete({
		
		source: function( request, response ) {
			
			jQuery.ajax({
				url: "",
				type: "POST",
				dataType: "json",
				data: {"option":"com_vbizz",  "task":"search", "tmpl":"component", "keyword":request.term},
				success: function(data) {
					response(jQuery.map(JSON.parse(data.search), function(item) {
					  
						return {
							label: item.label,
							category: item.category,
							view: item.view,
							task: item.task
						};
						
					}));
				}
			});
			
		},
		
		select: function(event, ui){
			var view = ui.item.view;
			var task = ui.item.task;
			
			jQuery('form[name="vcAdminForm"] #view').val(view);
			jQuery('form[name="vcAdminForm"] #task').val(task);
		},
		
		minlength:0
		
	});

});


jQuery(document).on('click','#sbmt',function() {
	
	var val = jQuery('form[name="vcAdminForm"] #vc_search').val();
	
	if(val=="")
	{
		alert('<?php echo JText::_('SEARCH_FIELD_EMPTY'); ?>');
		return false;
	}
	
	var view = jQuery('form[name="vcAdminForm"] #view').val();
	var task = jQuery('form[name="vcAdminForm"] #task').val();
	
	if(task != "" && task != "none" && task != "viewonly") {
		window.location.href = "index.php?option=com_vbizz&view="+view+"&task="+task;
	} else if(task=="viewonly") {
		window.location.href = "index.php?option=com_vbizz&view="+view;
	} else if(view=="") {
		window.location.href = "index.php?option=com_vbizz&view=search&search="+val;
	} else {
		window.location.href = "index.php?option=com_vbizz&view="+view+"&search="+val;
	}
	
	return false;
	
});


jQuery(document).on('keyup','input#vc_search',function(e) {
	if(e.keyCode == 8 || e.keyCode == 46) {
		jQuery('form[name="vcAdminForm"] #view').val('');
		jQuery('form[name="vcAdminForm"] #task').val('');
	}
});


</script>


<form action="" method="post" name="vcAdminForm" id="vcAdminForm">
<div class="vbz_search">
<div class="vc_search">
	<input type="text" name="vc_search" id="vc_search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="" class="text_area" >
	<button id="sbmt" class="btn"><i class="fa fa-search"></i></button>
</div>

<input type="hidden" name="option" id="option" value="com_vbizz" >
<input type="hidden" name="view" id="view" value="" >
<input type="hidden" name="task" id="task" value="" >
</div>
</form>