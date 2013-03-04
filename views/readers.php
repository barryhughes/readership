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
				<option value="trashselected"><?php _e('Completely unsubscribe', 'readership'); ?></option>
			</select>
			<input type="submit" name="doaction" class="button-secondary action" />
		</div>

		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php echo sprintf(__('%d readers &ndash; %d pages', 'readership'), $allReaders, $totalPages); ?>
			</span>
			<span class="pagination-links">
			<?php
				// First set link
				echo '<a href="'.ReadershipHelper::paginationLink(1).'">'.__('&laquo; First', 'readership').'</a>';

				// One before link
				if ($showPage > 1) {
					$oneBefore = $showPage - 1;
					echo '<a href="'.ReadershipHelper::paginationLink($oneBefore).'">'.$oneBefore.'</a>';
				}

				// Current page
				echo '<span class="currentpage">'.$showPage.'</span>';

				// One after link
				if ($showPage < ($totalPages)) {
					$oneAfter = $showPage + 1;
					echo '<a href="'.ReadershipHelper::paginationLink($oneAfter).'">'.$oneAfter.'</a>';
				}

				// Last link
				echo '<a href="'.ReadershipHelper::paginationLink($totalPages).'">'.__('Last &raquo;', 'readership').'</a>';
			?>
			</span>
		</div>
	</div>

	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" id="cb" class="check-column"><input type="checkbox" name="checkall" value="all" /></th>
				<th scope="col"><?php _e('Username', 'readership'); ?></th>
				<th scope="col"><?php _e('Email', 'readership'); ?></th>
				<th scope="col"><?php _e('Package Subscriptions', 'readership'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="cb" class="check-column"><input type="checkbox" name="checkall" value="all" /></th>
				<th scope="col"><?php _e('Username', 'readership'); ?></th>
				<th scope="col"><?php _e('Email', 'readership'); ?></th>
				<th scope="col"><?php _e('Package Subscriptions', 'readership'); ?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($readers) > 0): ?>
				<?php foreach ($readers as $reader):

					$id = esc_attr($reader->user()->ID);
					$displayName = esc_html($reader->user()->display_name);
					$loginName = esc_html($reader->user()->user_login);
					$email = esc_html($reader->user()->user_email);

					$editLink = ReadershipAdmin::getActionLink(array(
						'action' => 'edit',
						'id' => $id
					));
					$trashLink = ReadershipAdmin::getActionLink(array(
						'do_once' => 'unattach',
						'id' => $id
					));
					$trashLink = wp_nonce_url($trashLink, 'unattach');

					$subscriptions = $reader->listAssignedPackages();
					$subDetails = array();

					foreach ($subscriptions as $packageID => $subscription) {
						$package = ReadershipRegister::packageList()->getPackage($packageID);

						$packageLink = ReadershipAdmin::getActionLink(array(
							'subpage' => 'packages',
							'action' => 'edit',
							'id' => $package->id()
						));
						$outlineDetails = '<a href="'.$packageLink.'">'.$package->name().'</a> '
							.'<span class="id">(#'.$package->id().')</span> ';

						$outlineDetails .= ReadershipHelper::subscriptionTimeLine($reader, $packageID);
						$subDetails[] = $outlineDetails;
					}
				?>
				<tr id="packagerow-<?php echo $id; ?>">

					<th scope="row" class="check-column"><input type="checkbox" name="selected[]" value="<?php echo $id; ?>" /></th>
					<td>
						<strong><a href="<?php echo $editLink; ?>"><?php echo $displayName; ?></a></strong>
						<?php if ($loginName != $displayName) echo "($loginName)"; ?>
						<div class="row-actions">
							<span class="edit"><a href="<?php echo $editLink; ?>"><?php _e('View &amp; Edit', 'readership'); ?></a> | </span>
							<span class="delete"><a href="<?php echo $trashLink; ?>"><?php _e('Unsubscribe Completely', 'readership'); ?></a></span>
						</div>
					</td>
					<td><?php echo $email; ?></td>
					<td><span class="subscription-outline">
					<?php
						if (count($subscriptions) === 0) {
							_e('Not currently subscribed to any packages', 'readership');
						}
						else {
							echo '<ul>';

							foreach ($subDetails as $subscription)
								echo "<li>$subscription</li>";

							echo '</ul>';
						}
					?>
					</span></td>
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
		<h3 class='hndle'><span><?php _e('Add New Reader', 'readership'); ?></span></h3>
		<div class="inside">

			<p>
				<?php _e('You can assign subscription packages to existing WordPress users.', 'readership'); ?>
				<?php $openingATag = '<a href="'.get_admin_url(null, 'user-new.php').'">'; ?>
				<?php printf(__('If you need to create a completely new record then head over to the %s'
					.'add new user page%s first of all &ndash; then return here to assign a package to them.',
					'readership'), $openingATag, '</a>'); ?>
			</p>

			<div class="scrollablelist">
				<table>
				<?php foreach ($users as $user): ?>
					<?php $zebra = (isset($zebra) and $zebra === true) ? false : true; ?>
					<tr <?php echo $zebra ? 'class="stripe"' : ''; ?>>
						<td class="checkcolumn">
							<input type="checkbox" value="<?php echo esc_attr($user->ID); ?>" name="pullusers[]" />
						</td>
						<td>
							<?php echo esc_html($user->display_name); ?>
							(<?php echo esc_html($user->ID); ?>)
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if (count($users) === 0): ?>
					<tr>
						<td>
							<?php _e('No user accounts found (or they are all subscribing readers).', 'readership'); ?>
						</td>
					</tr>
				<?php endif; ?>
				</table>
			</div>

			<p>
				<?php _e('Select the subscription packages you wish to assign to the above '
					.'user(s)', 'readership'); ?>
			</p>

			<div class="scrollablelist">
				<table>
				<?php foreach ($packages as $package): ?>
					<?php $zebra = (isset($zebra) and $zebra === true) ? false : true; ?>
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
				<?php if (count($packages) === 0): ?>
					<tr>
						<td>
							<?php _e('No subscription packages found.', 'readership'); ?>
						</td>
					</tr>
				<?php endif; ?>
				</table>
			</div>

			<p><input type="submit" name="pullaccounts" value="Add Accounts" class="button-primary" /></p>

		</div> <!-- .inside -->
	</div> <!-- .postbox -->

</div> </div>
