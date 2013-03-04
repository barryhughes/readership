<?php
if (isset($messages))
	foreach ($messages as $message)
		foreach ($message as $type => $text) {
			echo '<div class="updatemsg '.$type.'">';
			echo esc_html($text);
			echo '</div>';
		}
?>

<!-- Outline details and save/update button -->
<div class="whole metabox-holder"> <div class="postbox-container">

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('General Settings', 'readership'); ?></span></h3>
		<div class="inside">

			<table class="horizontalform alignleft">
				<tr>
					<td><?php _e('Readership will try to add visibility controls to all of the following '
						.'content types (when selected)', 'readership'); ?></td>
					<td><?php _e('Auto-subscription can be used to assign a basic package to every new '
						.'user account that is created', 'readership'); ?></td>
					<td><?php _e('For experienced users: use these settings to tweak Readership&#146;s '
						.'behaviour.', 'readership'); ?></td>
				</tr>
				<tr>
					<td><div class="scrollablelist">
						<table>
							<?php foreach ($postTypes as $postType => $postTypeData): ?>
							<?php $zebra = (isset($zebra) and $zebra === true) ? false : true; ?>
							<tr <?php echo $zebra ? 'class="stripe"' : ''; ?>>
								<td class="checkcolumn">
									<input type="checkbox" value="<?php echo esc_attr($postType); ?>" name="posttypes[]" <?php
										echo ReadershipSettings::isSupportedType($postType) ? 'checked="checked"' : '' ?> />
								</td>
								<td>
									<?php echo esc_html($postTypeData->labels->name); ?>
									(<?php echo esc_html($postType); ?>)
								</td>
							</tr>
							<?php endforeach; ?>
							<?php if (count($postTypes) === 0): ?>
							<tr>
								<td>
									<?php _e('No post types detected!', 'readership'); ?>
								</td>
							</tr>
							<?php endif; ?>
						</table>
					</div></td>
					<td>
						<p><label for="autosubpackage"><?php _e('Default package', 'readership'); ?></label>
							<br />
						<?php if (count($packages) === 0): ?>
							<input type="text" name="blankpackage" value="<?php _e('No packages found', 'readership'); ?>"
						        readonly="readonly" />
						<?php else: ?>
							<select name="autosubpackage">
							<?php foreach ($packages as $package): ?>
								<option value="<?php echo esc_attr($package->id()); ?>" <?php
									echo ($defaultPackage == $package->id()) ? 'selected="selected"' : ''; ?>>
									<?php echo esc_html($package->name()); ?>
								</option>
							<?php endforeach; ?>
							</select>
						<?php endif; ?></p>

						<p><input type="checkbox" name="autosubscribe" value="1" <?php
							echo $autoSubscribe ? 'checked="checked" ' : ''; ?>/>
						<label for="autosubscribe"><?php _e('Turn auto-subscription on', 'readership'); ?></label></p>
					</td>
					<td>
						<input type="checkbox" name="sessionmarker" value="1" <?php
							echo $sessionMarker ? 'checked="checked" ' : ''; ?> />
						<label for="sessionmarker"><?php _e('Create a session variable for subscribed users', 'readership'); ?></label>
					</td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td>
						<div class="savebox">
							<?php _e('Save changes!', 'readership'); ?> <br />
							<input type="submit" name="save" value="<?php _e('Update Settings', 'readership'); ?>"
							       class="button-primary" /> <br />
						</div>
					</td>
				</tr>
			</table>



		</div>

</div> </div>


<!-- Record import/export tools -->
<div class="whole metabox-holder"> <div class="postbox-container">

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Import/Export', 'readership'); ?></span></h3>
		<div class="inside">
			<p>Oh hello</p>
		</div>
	</div>

</div> </div>