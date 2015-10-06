<?php

namespace AU\Analytics;

function upgrades() {
	if (!elgg_is_admin_logged_in()) {
		return true;
	}
	
	require_once __DIR__ . '/upgrades.php';
	
	run_function_once(__NAMESPACE__ . '\\upgrade20151005');
}
