<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'vendor/uvii/samesitenone/src/SameSiteNone.php';

use Uvii\SameSiteNone;

class Samesite_cookies_ext
{
	
	public $settings = array();
	public $name = 'SameSite Cookies';
	public $version = '1.3.1';
	public $description = 'Add SameSite attribute to ExpressionEngine cookies';
	public $settings_exist = 'y';
	public $docs_url = '';
	
	
	public function __construct($settings = array())
	{

		$this->settings = $settings;

	}

	public function activate_extension()
	{
		ee()->db->insert(
			'extensions',
			array(
				'class' => __CLASS__,
				'method' => 'set_cookie_end',
				'hook' => 'set_cookie_end',
				'settings' => serialize($this->settings),
				'priority' => 10,
				'version' => $this->version,
				'enabled' => 'y',
			)
		);
	}
	
	public function update_extension($current = '')
	{
		if ( ! $current || $current === $this->version)
		{
			return FALSE;
		}
		
		ee()->db->where('class', __CLASS__);
		ee()->db->update(
					'extensions',
					array('version' => $this->version)
		);
		
	}

	public function disable_extension()
	{
		ee()->db->delete('extensions', array('class' => __CLASS__));
	}
	
	function settings()
	{
		$settings = array();
		
		$settings['default_cookies'] = array('s', array('' => 'default', 'None' => 'None', 'Lax' => 'Lax', 'Strict' => 'Strict'), '');
		$settings['samesite_none'] = array('t', array('rows' => '20'), '');
		$settings['samesite_lax'] = array('t', array('rows' => '20'), '');
		$settings['samesite_strict'] = array('t', array('rows' => '20'), '');
		$settings['secure_samesite_none'] = array('s', array('yes' => lang('Yes'), 'no' => lang('No')), 'yes');

		return $settings;
	}
	
	
	/**
	 * Take control of setting cookies after cookie parameters have been normalized according to the cookie configuration settings.
	 *
	 * @param array: Array of prepped cookie parameters, which include the following keys: prefix, name, value, expire, path, domain, secure_cookie
	 * @return bool
	 */
	public function set_cookie_end($data)
	{
		$return = FALSE;
		
		$cookie_name = $data['prefix'].$data['name'];
		$cookies = array(
			'none' => array(),
			'lax' => array(),
			'strict' => array(),
		);
		
		// Just in case this becomes available in the future
		if ( ! isset($data['samesite']))
		{
			$data['samesite'] = '';
		}
		
		// Not all browsers are compatible with SameSite=None
		// https://www.chromium.org/updates/same-site/incompatible-clients
		$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$SameSiteNoneSafe = SameSiteNone::isSafe($userAgent);
		
		$cookie_found = FALSE;
		
		foreach ($cookies as $key => $val)
		{
			$setting_name = 'samesite_'.$key;
			if (isset($this->settings[$setting_name]) && ! empty($this->settings[$setting_name]))
			{
				$setting = explode("\n", str_replace(",", "\n", trim($this->settings[$setting_name])));
				$cookies[$key] = array_map('trim', $setting);
				if (in_array($cookie_name, $setting))
				{
					$cookie_found = TRUE;
				}
			}
		}

		if ($cookie_found)
		{
			foreach ($cookies as $key => $val)
			{
				$setting_name = 'samesite_'.$key;
				$samesite_attr = ucfirst($key);
				if (in_array($cookie_name, $val))
				{
					if ($key == 'none')
					{
						// Not all browsers are compatible with SameSite=None
						$data['samesite'] = ($SameSiteNoneSafe ? $samesite_attr : '');
						// Some browsers may block cookies set with `SameSite=None` but without `Secure`.
						if (isset($this->settings['secure_cookies']) && $this->settings['secure_cookies'] === 'yes')
						{
							$data['secure_cookie'] = 1;
						}
					}
					else
					{
						$data['samesite'] = $samesite_attr;
					}
				}
			}
			
			$return = $this->set_samesite_cookie($data, $SameSiteNoneSafe);
			ee()->extensions->end_script = TRUE;
			
		}
		else if (isset($this->settings['default_cookies']) && $this->settings['default_cookies'] !== '')
		{
			if ($this->settings['default_cookies'] == 'None')
			{
				$data['samesite'] = ($SameSiteNoneSafe ? $this->settings['default_cookies'] : '');
			}
			else
			{
				$data['samesite'] = $this->settings['default_cookies'];
			}
			
			$return = $this->set_samesite_cookie($data);
			ee()->extensions->end_script = TRUE;
		}
		
		return $return;
	}
	
	
	private function set_samesite_cookie($data, $SameSiteNoneSafe=TRUE)
	{
		if (PHP_VERSION_ID < 70300) {
			// Older versions of PHP do not support an array as the 3rd parameter,
			// thus the SameSite setting must be hacked in with the path option.
			return setcookie($data['prefix'].$data['name'], $data['value'],
				$data['expire'],
				$data['path'] . ($SameSiteNoneSafe === TRUE ? '; SameSite=' . $data['samesite'] : ''),
				$data['domain'],
				$data['secure_cookie'],
				$data['httponly']
			);
		} else {
			$cookieParams = [
				'expires' => $data['expire'],
				'path' => $data['path'],
				'domain' => $data['domain'],
				'secure' => $data['secure_cookie'],
				'httponly' => $data['httponly']
			];
			if ($SameSiteNoneSafe === TRUE) {
				$cookieParams['samesite'] = $data['samesite'];
			}
			return setcookie($data['prefix'].$data['name'], $data['value'], $cookieParams);
		}
		return FALSE;
	}
	
}