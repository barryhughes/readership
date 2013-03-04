<?php 
if (isset($messages)) 
	foreach ($messages as $message)
		foreach ($message as $type => $text) {
			echo '<div class="updatemsg '.$type.'">';
			echo esc_html($text);
			echo '</div>';
		}
?>

<div class="twothirds">

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="actions">
				<option value=""><?php _e('Select action&hellip;', 'readership'); ?></option>
				<option value="trashselected"><?php _e('Delete selected', 'readership'); ?></option>
			</select>
			<input type="submit" name="doaction" class="button-secondary action" />
		</div>
		
		<div class="alignright">
			<span class="displaying-num">
				<?php echo sprintf(__('%d packages', 'readership'), count($packages)); ?>
			</span>
		</div>
	</div>

	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" id="cb" class="check-column"><input type="checkbox" name="checkall" value="all" /></th>
				<th scope="col"><?php _e('Package Name', 'readership'); ?></th>
				<th scope="col"><?php _e('Timespan', 'readership'); ?></th>
				<th scope="col"><?php _e('Recurring', 'readership'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col"><input type="checkbox" name="checkall" value="all" /></th>
				<th scope="col"><?php _e('Package Name', 'readership'); ?></th>
				<th scope="col"><?php _e('Timespan', 'readership'); ?></th>
				<th scope="col"><?php _e('Recurring', 'readership'); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($packages) > 0): ?>
				<?php foreach ($packages as $package): 
				
					$id = esc_attr($package->id());
					$name = esc_html($package->name());
					$interval = esc_html($package->getReadableInterval());
					$recurring = ($package->shouldRenew() === true) 
						? __('Yes', 'readership')
						: __('No', 'readership');
					$editLink = ReadershipAdmin::getActionLink(array(
						'action' => 'edit',
						'id' => $id
					));
					$statsLink = ReadershipAdmin::getActionLink(array(
						'action' => 'stats',
						'id' => $id
					));
					$trashLink = ReadershipAdmin::getActionLink(array(
						'do_once' => 'delete',
						'id' => $id
					));
					$trashLink = wp_nonce_url($trashLink, 'deletepackage');
				?>
				<tr id="packagerow-<?php echo $id; ?>">
					
					<th scope="row" class="check-column"><input type="checkbox" name="selected[]" value="<?php echo $id; ?>" /></th>
					<td>
						<strong><a href="<?php echo $editLink; ?>"><?php echo $name; ?></a></strong>
						<div class="row-actions">
							<span class="edit"><a href="<?php echo $editLink; ?>"><?php _e('View &amp; Edit', 'readership'); ?></a> | </span>
							<span class="delete"><a href="<?php echo $trashLink; ?>"><?php _e('Delete', 'readership'); ?></a></span>
						</div>
					</td>
					<td><?php echo $interval; ?></td>
					<td><?php echo $recurring; ?></td>
				</tr>
				<?php endforeach; ?>
			<?php else: ?>
			<tr>
				<td colspan="4"><?php _e('No packages were found', 'readership'); ?></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

</div>

<div class="third metabox-holder"> <div class="postbox-container">
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Create New Package', 'readership'); ?></span></h3>
		<div class="inside">
		
			<table class="form">
				<tr>
					<td>
						<label for="name"><?php _e('Name', 'readership'); ?></label>
					</td>
					<td>
						<input type="text" name="name" value="<?php //echo esc_attr($newName); ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="interval"><?php _e('Timespan', 'readership'); ?></label>
					</td>
					<td>
						<input type="text" name="interval" value="<?php //echo esc_attr($newInterval); ?>" />
						<select name="intervalmeasure">
							<option value="years"><?php _e('years', 'readership'); ?></option>
							<option value="months"><?php _e('months', 'readership'); ?></option>
							<option value="weeks"><?php _e('weeks', 'readership'); ?></option>
							<option value="days"><?php _e('days', 'readership'); ?></option>
						</select>
					</td>
				</tr>
				<tr class="penultimaterow">
					<td> &nbsp;	</td>
					<td>
						<input type="checkbox" name="renew" value="on" <?php //echo $newRenewState; ?> />
						<label for="renew"><?php _e('Recurring', 'readership'); ?></label>
					</td>
				</tr>
				<tr class="finalrow">
					<td> &nbsp; </td>
					<td>
						<input type="submit" name="newsave" value="<?php _e('Create Package', 'readership'); ?>" 
							class="button-primary" />
					</td>
				</tr>
			</table>
		</div>
	</div>
	
</div> </div>
