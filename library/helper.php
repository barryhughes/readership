<?php
class ReadershipHelper {
	public static function subscriptionTimeLine(ReadershipReader $reader, $packageID) {
		$daysLeft = $reader->timeRemainingOnPackage($packageID);
		$startDate = $reader->packageStartDate($packageID);

		$dire = apply_filters('readershipDaysLeftDire', 1);
		$close = apply_filters('readershipDaysLeftClose', 31);

		if (ReadershipDate::isInTheFuture($startDate)) {
			$daysLeft = $reader->timeUntilPackageStarts($packageID);
			$condition = 'blue';
		}
		elseif ($daysLeft === -1 or $daysLeft > $close) {
			$condition = 'green';
		}
		elseif ($daysLeft <= $close and $daysLeft > $dire) {
			$condition = 'amber';
		}
		else {
			$condition = 'red';
		}

		$daysRemaining = '<span class="remaining '.$condition.'">';

		if ($condition === 'blue') $daysRemaining .= sprintf(__('%d days until start', 'readership'), $daysLeft);
		elseif ($daysLeft === -1) $daysRemaining .= __('does not expire', 'readership');
		elseif ($daysLeft === 0) $daysRemaining .= __('subscription has expired', 'readership');
		else $daysRemaining .= sprintf(__('%d days remaining', 'readership'), $daysLeft);

		return $daysRemaining .= '</span>';
	}


	public function paginationLink($page, $perPage = null) {
		$params = (array) $_GET;
		$params['showpage'] = $page;
		if ($perPage !== null) $params['resultset'] = $perPage;

		return ReadershipAdmin::getActionLink($params);
	}
}