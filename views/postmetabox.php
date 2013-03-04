<div class="wrap readership">

<?php wp_nonce_field('readershipPostMeta', 'subscriptionSettings'); ?>

	<p><?php _e('Restrict this content to those who have subscribed to the following packages',
		'readership'); ?></p>

	<div class="scrollablelist">
		<table>
		<?php foreach ($packages as $package):

			echo ( (isset($zebra) and $zebra === true) ? false : true)
				? '<tr class="stripe">' : '<tr>';

			$id = esc_attr($package->id());
			$name = esc_html($package->name());
			$checked = in_array($id, $allocatedPackages) ? 'checked="checked"' : '';

		?>
				<td class="checkcolumn">
					<input type="checkbox" value="<?php echo $id; ?>" name="postreadershippackages[]" <?php echo $checked; ?> />
				</td>
				<td>
					<?php echo $name; ?>
					(<?php echo $id; ?>)
				</td>
			</tr>
		<?php endforeach; ?>
		<?php if (count($packages) === 0): ?>
			<tr>
				<td>
					<?php _e('No packages detected! Please set up a subscription package before '
					.'trying to use content controls.', 'readership'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</table>
	</div>

	<p><?php _e('Changed something? Don&#146;t forget to hit the update button!',
		'readership'); ?></p>

</div>