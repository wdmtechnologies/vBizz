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

function VbizzBuildRoute( &$query )
{
	$db = JFactory::getDBO();
	
	$segments = array();
	
	$app = JFactory::getApplication();
	
	$menu = $app->getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
	//echo '<pre>'; print_r($query); jexit();
	if(isset($query['view']))	{
		
		switch($query['view'])	{
		case 'support':
		   $segments[] = $query['view'];
			   if(isset($query['layout']))	
			   {
				switch($query['layout'])
				{
				case 'topics':
				$segments[] = $query['layout'];
				      if(isset($query['category']) and $query['category']>0){
								
								$segments[] = $query['category'];
								unset($query['category']);
							} 
				
				unset($query['layout']);
				break;
				case 'modal':
				$segments[] = $query['layout'];
				      if(isset($query['category']) and $query['category']>0){
								$segments[] = $query['category'];
								unset($query['category']);
							} 
				
				unset($query['layout']);
				break;
				case 'replies':
				$segments[] = $query['layout'];
				      if(isset($query['category']) and $query['category']>0){
								$segments[] = $query['category'];
								unset($query['category']);
							} 
				         if(isset($query['topic']) and $query['topic']>0){
								$segments[] = $query['topic'];
								unset($query['topic']);
							}
				unset($query['layout']);
				break;
				default :	
				 
					if(isset($query['cid']) and $query['cid'][0]>0)	{
						$segments[] = JText::_('EDIT');
						
						$segments[] = $query['cid'][0];
						
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = 'new';
						unset($query['cid']);
					}
					else	
						$segments[] = JText::_('NEW');
						
					unset($query['layout']);
				
				 break;
				}  
			   }
             break;
			case 'customer':
			$segments[] = 'customer';
			if(isset($query['task']))
			{
			 $segments[] = $query['task'];
			 if(isset($query['cid']) and $query['cid'][0]>=0){
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
				elseif(isset($query['cid']))	{
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
			  unset($query['task']);				 
			}
			 break;
			 case 'leads':
			$segments[] = 'leads';
			if(isset($query['task']))
			{
			 $segments[] = $query['task'];
			 if(isset($query['cid']) and $query['cid'][0]>=0){
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
				elseif(isset($query['cid']))	{
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
			  unset($query['task']);				 
			}
			 break;
			 case 'vbizz':
			$segments[] = 'vbizz';
			if(isset($query['task']))
			{
			 $segments[] = $query['task'];
			 if(isset($query['cid']) and $query['cid'][0]>=0){
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
				elseif(isset($query['cid']))	{
					$segments[] = $query['cid'][0];
					unset($query['cid']);
				}
			  unset($query['task']);				 
			}
			 break;
			case 'vendor':
			    $segments[] = 'vendor';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>=0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				}
			    
            break;
			case 'reports':	
					$segments[] = 'reports';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>=0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				}
			  break;	
            case 'income':
			    $segments[] = 'order';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				}
			    
            break;
            case 'expense':
			  $segments[] = 'purchase';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				}  
            break;	
            case 'invoices':
			    $segments[] = 'invoices';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				} 
            break;
            case 'invoicesexpense':
			$segments[] = 'invoicesexpense';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				} 
            break;
             case 'edept':
			  $segments[] = 'edept';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				} 
            break;	
             case 'edesg':
			  $segments[] = 'edesg';
				if(isset($query['task']))
				{
				 $segments[] = $query['task'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['task']);				 
				} 
            break;
            case 'milestone':
			  $segments[] = 'milestone';
				if(isset($query['layout']))
				{
				 $segments[] = $query['layout'];
				 if(isset($query['cid']) and $query['cid'][0]>0){
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = $query['cid'][0];
						unset($query['cid']);
					}
                  unset($query['layout']);				 
				} 
            break;				
			default :
				
				$segments[] = $query['view'];
								
				if(isset($query['layout']))	{
					
					if(isset($query['cid']) and $query['cid'][0]>0)	{
						$segments[] = JText::_('EDIT');
						
						$segments[] = $query['cid'][0];
						
						unset($query['cid']);
					}
					elseif(isset($query['cid']))	{
						$segments[] = 'new';
						unset($query['cid']);
					}
					else	
						$segments[] = JText::_('NEW');
						
					unset($query['layout']);
				}
				
			break;
		
		}
		
		unset($query['view']);
	}
	
	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/banners/task/bid/Itemid
 *
 * index.php?/banners/bid/Itemid
 */
function VbizzParseRoute( $segments )
{	
	$db =  JFactory::getDBO();
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);
	
	if ($count)
	{
		
		$segments[0] = str_replace(':', '-', $segments[0]);
		
		switch($segments[0])	{
			case 'support':
			$vars['view'] = $segments[0];
			$count--;
			array_shift( $segments );
			if($count)	{
				
					switch($segments[0]){
					case 'topics':
					$vars['layout'] = $segments[0];
					$count--;
					array_shift( $segments );
					     if($count)
							 {
								$vars['category'] = $segments[0];	
									$count--;
									array_shift( $segments ); 
							     
							 }
					break;
					case 'replies':
					$vars['layout'] = $segments[0];
					$count--;
					array_shift( $segments );
					     if($count)
							 {
								$vars['category'] = $segments[0];	
									$count--;
									array_shift( $segments ); 
							     
									if($count)
									{
									$vars['topic'] = $segments[0];	
									$count--;
									array_shift( $segments ); 

									}
							 }
					break;
					case 'model':
					$vars['layout'] = $segments[0];
					$count--;
					array_shift( $segments );
					     if($count)
							 {
								$vars['category'] = $segments[0];	
									$count--;
									array_shift( $segments ); 
							     
							 }
					break;
					default:	
					$vars['layout'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
					if($count)	{
						
						$vars['cid'][] = $segments[0];
						
						$count--;
						array_shift( $segments );
					}
			       break;
				  }
				}
			 break;
            case 'order':	
					$vars['view'] = 'income';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
			case 'customer':	
					$vars['view'] = 'customer';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
			    break;
				case 'leads':	
					$vars['view'] = 'leads';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
			    break;
			 case 'vendor':	
					$vars['view'] = 'vendor';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;		
            case 'reports':	
					$vars['view'] = 'reports';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;		 
            case 'purchase':	
					$vars['view'] = 'expense';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
            case 'invoices':	
					$vars['view'] = 'invoices';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
            case 'invoicesexpense':	
					$vars['view'] = 'invoicesexpense';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;	
            case 'edept':	
					$vars['view'] = 'edept';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
			case 'vbizz':	
					$vars['view'] = 'vbizz';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
            case 'edesg':	
					$vars['view'] = 'edesg';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['task'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;
            case 'milestone':	
					$vars['view'] = 'milestone';
					$count--;
					array_shift( $segments );
					if($count)	{
				
					$vars['layout'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
						if($count)	{
							
							$vars['cid'][] = $segments[0];
							
							$count--;
							array_shift( $segments );
						}
				
				    }
            break;			
			default:
				
				$vars['view'] = $segments[0];
				
				$count--;
				array_shift( $segments );
				
				if($count)	{
				
					$vars['layout'] = $segments[0];
					
					$count--;
					array_shift( $segments );
					
					if($count)	{
						
						$vars['cid'][] = $segments[0];
						
						$count--;
						array_shift( $segments );
					}
				
				}
			
			break;
						
		}
		
	}

	return $vars;
}