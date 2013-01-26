<?php

function _e($s) {
	trigger_error($s,E_USER_ERROR);
}
function _assert($condition,$description) {
	if (!$condition)
		_e($description);
}

?>
