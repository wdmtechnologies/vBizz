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

$mainframe = JFactory :: getApplication('site');

$sitename	= $mainframe->getCfg( 'sitename' );
$MetaDesc	= $mainframe->getCfg( 'MetaDesc' );

?>


  <link rel="stylesheet" href="http://okhlites.com/demo/vbizz2.1.0/components/com_vbizz/assets/css/vbizz.css" type="text/css" />
  <link rel="stylesheet" href="/demo/vbizz2.1.0/media/jui/css/chosen.css" type="text/css" />
  
  <script src="components/com_vbizz/assets/js/jquery.1.10.js" type="text/javascript"></script>
  <script src="/demo/vbizz2.1.0/media/jui/js/chosen.jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">

	jQuery(document).ready(function (){
		jQuery('select').chosen({"disable_search_threshold":10,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	});
  </script>

<link href="/demo/vbizz2.1.0/templates/vacount/css/style.css" rel="stylesheet" type="text/css" />
<link href="/demo/vbizz2.1.0/templates/vacount/css/icomoon.css" rel="stylesheet" type="text/css" />
<link href="/demo/vbizz2.1.0/templates/vacount/css/responsive.css" rel="stylesheet" type="text/css" />

