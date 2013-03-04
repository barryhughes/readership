<div class="wrap readership">

<div class="icon32"></div>
<h2><?php _e('Readership', 'readership'); ?></h2>

<ul class="subsubsub">
	<?php foreach ($menu->items as $title => $link): ?>
	<li>
	<?php
		// Separator bars/pipes
		$notFirst = isset($notFirst) ? true : false;
		echo $notFirst ? ' | ' : '';

		// Current class?
		$current = ($menu->highlighted === $link) ? 'class="current"' : '';
	?>
		<a href="<?php echo $link; ?>" <?php echo $current; ?>><?php echo $title; ?></a>
	</li>
	<?php endforeach; ?>
</ul>

<div class="content">
	<form action="<?php echo $formAction; ?>" method="post">
		<?php 
			echo $page; 
			wp_nonce_field('readership', 'readership');
		?>
	</form>
</div>

</div>