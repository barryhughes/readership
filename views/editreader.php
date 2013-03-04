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
		<h3 class='hndle'><span><?php _e('Reader Account', 'readership'); ?></span></h3>
		<div class="inside">

			<table class="horizontalform alignleft">
				<tr>
					<td>
						<label for="name"><?php _e('Display Name', 'readership'); ?></label>
					</td>
					<td>
						<label for="login"><?php _e('User Login', 'readership'); ?></label>
					</td>
					<td>
						<label for="email"><?php _e('Email Address', 'readership'); ?></label>
					</td>
					<td> </td>
				</tr>
				<?php
					$login = esc_html($reader->user()->user_login);
					$name = esc_html($reader->user()->display_name);
					$email = esc_html($reader->user()->user_email);
				    $readerID = esc_attr($reader->user()->ID);
					$editUserLink = esc_attr(get_admin_url(null, 'user-edit.php?'
						.http_build_query(array('user_id' => $readerID))));
				?>
				<tr>
					<td>
						<input type="text" readonly="readonly" name="name" value="<?php echo $name; ?>" />
					</td>
					<td>
						<input type="text" readonly="readonly" name="login" value="<?php echo $login; ?>" />
					</td>
					<td>
						<input type="text" readonly="readonly" name="email" value="<?php echo $email; ?>" />
					</td>
					<td><?php if (empty($email) !== true): ?>
						<a href="mailto:<?php echo esc_attr($email); ?>">Send an email to this user</a>
					<?php endif; ?></td>
				</tr>
			</table>

			<table class="horizontalform alignright">
				<tr>
					<td>
						<input type="hidden" name="current-reader" value="<?php echo $readerID; ?>" />
						<input type="submit" name="save" value="<?php _e('Update Record', 'readership'); ?>"
							class="button-primary" /> <br />
					</td>
				</tr>
				<tr>
					<td> <a href="<?php echo $editUserLink; ?>">Edit the user settings</a></td>
				</tr>
			</table>
		</div>
	</div>

</div> </div>

<!-- Package subscription table -->
<div class="twothirds">

	<p><?php _e('You can easily extend or forcibly expire subscriptions by changing the '
		.'start date or clicking the Extend or Remove quick links provided below each '
		.'package name.', 'readership'); ?></p>

	<table class="widefat">
		<thead>
		<tr>
			<th scope="col"><?php _e('Package Name', 'readership'); ?></th>
			<th scope="col"><?php _e('Start Date (mm/dd/yyyy)', 'readership'); ?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th scope="col"><?php _e('Package Name', 'readership'); ?></th>
			<th scope="col"><?php _e('Start Date (mm/dd/yyyy)', 'readership'); ?></th>
		</tr>
		</tfoot>
		<tbody>

		<?php if (count($packages) > 0): ?>
			<?php foreach ($packages as $package):

				$id = esc_attr($package->id());
				$name = esc_html($package->name());
				$interval = esc_html($package->getReadableInterval());
				$date = esc_attr($reader->packageStartDate($id)->format('m/d/Y'));
				$daysRemaining = ReadershipHelper::subscriptionTimeLine($reader, $id);

				$trashLink = ReadershipAdmin::getActionLink(array(
					'action' => 'edit',
					'id' => $readerID,
					'do_once' => 'unsubscribe-'.$id
				));
				$trashLink = wp_nonce_url($trashLink, 'unsubscribe');
				?>
			<tr id="packagerow-<?php echo $id; ?>">

				<td>
					<strong><a href="<?php echo $editLink; ?>"><?php echo $name; ?></a>
						(<?php echo $interval; ?>)
					</strong>
					<div class="row-actions">
						<span class="delete"><a href="<?php echo $trashLink; ?>"><?php _e('Remove subscription', 'readership'); ?></a></span>
					</div>
				</td>
				<td>
					<input type="text" name="startdate[<?php echo $id; ?>]" value="<?php echo $date; ?>" class="datefield" />
					<span class="subscription-outline"><?php echo $daysRemaining; ?></span>
				</td>
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

<!-- Package assignment -->
<div class="third metabox-holder"> <div class="postbox-container">

	<div class="postbox">
		<h3 class='hndle'><span><?php _e('Create New Package', 'readership'); ?></span></h3>
		<div class="inside">

			<p>
				<?php _e('Select the subscription packages you wish to assign to the this '
				.'user', 'readership'); ?>
			</p>

			<div class="scrollablelist">
				<table>
					<?php foreach ($availablePackages as $package): ?>
					<?php $zebra = $zebra === true ? false : true; ?>
					<tr <?php echo $zebra ? 'class="stripe"' : ''; ?>>
						<td class="checkcolumn">
							<input type="checkbox" value="<?php echo esc_attr($package->id()); ?>" name="assignables[]" />
						</td>
						<td>
							<?php echo esc_html($package->name()); ?>
							(<?php echo esc_html($package->id()); ?>)
						</td>
					</tr>
					<?php endforeach; ?>
					<?php if (count($availablePackages) === 0): ?>
					<tr>
						<td>
							<?php _e('No subscription packages found (or the user is already assigned to all of the '
							.'available packages).', 'readership'); ?>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</div>

			<p><input type="submit" name="save" value="Add Package" class="button-primary" />
			<?php _e('&hellip; and update this record', 'readership'); ?></p>

		</div>
	</div>

</div> </div>