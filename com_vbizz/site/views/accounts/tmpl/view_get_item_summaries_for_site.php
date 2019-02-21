<?php 
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 


if(is_array($response)): ?>
	<table class="table table-bordered">
		<thead>
			<tr>
				<td class="c-gray"><b><?php echo JText::_('ADDED_ACCOUNTS'); ?></b></td>
			</tr>
		</thead>
		<tbody>
			<?php if(count($response)>0): ?>
				<?php foreach($response as $item): ?>
					<tr>
						<td>
							<img src="<?php echo JURI::root().'components/com_vbizz/assets/images/siteImage.fastlinksb.gif'; ?>"> <?= $item->itemDisplayName ?>
							<?php if(isset($item->itemData)): ?>
								<?php if(count($item->itemData->accounts)>0): ?>
									<?php if(isset($item->itemData->accounts[0]->totalBalance)): ?>
										 <?php 
										 	$amount = $item->itemData->accounts[0]->totalBalance->amount;
										 	$currencyCode = $item->itemData->accounts[0]->totalBalance->currencyCode;
										 ?>
										 <span class="pull-right"><?= sprintf("%s- %s",$currencyCode, number_format($amount,2,',','.')) ?></span>
									<?php else: ?>
										<span class="pull-right">--</span>
									<?php endif; ?>
								<?php else: ?>
									<span class="pull-right">--</span>
								<?php endif; ?>
							<?php else: ?>
								<span class="pull-right">--</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="2"><b><?php echo JText::_('NO_RESULT'); ?></b></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
<?php else: ?>
	<?php echo JText::_('NO_RESULT'); ?>
<?php endif; ?>