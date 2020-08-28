<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(

	'default_cookies' => 'Enforce site-wide SameSite cookie attribute:
		<br><small><i>See <a href="' . ee('CP/URL', 'settings/security-privacy') . '">Security & Privacy</a> for other settings</i></small>',
	'samesite_none' => 'None - Apply SameSite=None to entered cookies:
		<br><small><i>Enter each cookie on a new line</i></small>',
	'samesite_lax' => 'Lax - Apply SameSite=Lax to entered cookies: 
		<br><small><i>Enter each cookie on a new line</i></small>',
	'samesite_strict' => 'Strict - Apply SameSite=Strict to entered cookies: 
		<br><small><i>Enter each cookie on a new line</i></small>',
	'secure_samesite_none' => 'Make SameSite=None cookies secure:
		<br><small><i>Browsers may block cookies set with `SameSite=None` but without `Secure`.</i></small>',
	
);

