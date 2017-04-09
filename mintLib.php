<?php

function getMintDetail($configArray) {
	$toReturn = false;
	$cmd = $configArray['mintApi'] . ' --session ' . $configArray['ius_session'] . ' --thx_guid ' . $configArray['thx_guid'] . ' ' . $configArray['username'] . ' ' . $configArray['password'] . ' 2> /dev/null';

	$result = `$cmd`;
	$resultObj = json_decode($result);

	if (is_array($resultObj) && (sizeof($resultObj) > 0)) {
		$toReturn = $resultObj;
	}

	return $toReturn;
}

function getMintBudgetDetail($configArray) {
	$toReturn = false;
	$cmd = $configArray['mintApi'] . ' --budgets --session ' . $configArray['ius_session'] . ' --thx_guid ' . $configArray['thx_guid'] . ' ' . $configArray['username'] . ' ' . $configArray['password'] . ' 2> /dev/null';

	$result = `$cmd`;

	$resultObj = json_decode($result);

	if (is_object($resultObj)) {
		$toReturn = $resultObj;
	}

	return $toReturn;
}

function getBalString($mintDetailObj,$type=false) {
	if (!$mintDetailObj) {
		return false;
	}
	$toReturn = '';
	$filterArray = array('bank','credit','investment','loan');
	if ($type) {
		if ($type == 'mortgage') {
			$type = 'loan';
		}
		if ($type == 'credit card') {
			$type = 'credit';
		}
		$filterArray = array($type);
	}
	foreach($mintDetailObj as $accountObj) {
		if (in_array($accountObj->accountType,$filterArray)) {
			if ($accountObj->isActive && !$accountObj->isClosed) {
				$toReturn .= "Balance for " . $accountObj->fiLoginDisplayName . " "
						. $accountObj->yodleeName . " is "
						. '$' . $accountObj->value . ".\n";
			}
		}
	}
	return $toReturn;
}
?>
