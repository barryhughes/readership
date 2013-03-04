<?php 
if (isset($messages)) 
	foreach ($messages as $message)
		foreach ($message as $type => $text) {
			echo '<div class="updatemsg '.$type.'">';
			echo esc_html($text);
			echo '</div>';
		}
?>

<div class="twothirds metabox-holder"> <div class="postbox-container">
	
	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Package Setup', 'readership'); ?></span></h3>
		<div class="inside">
		
			<table class="horizontalform">
				<tr>
					<td>
						<label for="name"><?php _e('Name', 'readership'); ?></label>
					</td>
					<td>
						<label for="interval"><?php _e('Timespan', 'readership'); ?></label>
					</td>
					<td>
						<input type="hidden" name="packageid" value="<?php echo esc_attr($package->id()); ?>" />
					</td>
					<td>
						<input type="submit" name="save" value="<?php _e('Update Package', 'readership'); ?>" 
							class="button-primary" /> <br />
					</td>
				</tr>
				<?php
					$name = $package->name();
					$interval = $package->getIntervalBreakdown();
					$period = $interval[0];
					$selectedMeasure = $interval[1];
					$measures = array('years', 'months', 'weeks', 'days');
					$recurring = ($package->shouldRenew() === true) 
						? 'checked="checked"' : '';
				?>
				<tr>
					<td>
						<input type="text" name="name" value="<?php echo esc_attr($name); ?>" class="med" />	
					</td>
					<td>
						<input type="text" name="interval" value="<?php echo esc_attr($period); ?>" class="short" />
						<select name="intervalmeasure">
						<?php foreach ($measures as $measure): ?>
							<option value="<?php echo esc_attr($measure); ?>"
								<?php if ($selectedMeasure === $measure) echo 'selected="selected "'; ?>>
								<?php _e($measure, 'readership'); ?>
							</option>
						<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="checkbox" name="renew" value="on" <?php echo $recurring; ?> />
						<label for="renew"><?php _e('Recurring', 'readership'); ?></label>
					</td>
					<td>
						<a href="<?php echo ReadershipAdmin::getActionLink(array()); ?>" class="altaction">
							<?php _e('&hellip; Or go back!', 'readership'); ?>
						</a>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
</div> </div>
