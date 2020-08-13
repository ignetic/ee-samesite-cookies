<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Samesite_cookies_ext
{
	
	public $settings = array();
	public $name = 'SameSite Cookies';
	public $version = '1.1';
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
		
		$settings['samesite'] = array('s', array('' => 'default', 'None' => 'None', 'Lax' => 'Lax', 'Strict' => 'Strict'), 'None');
		$settings['all_cookies'] = array('s', array('apply_selected' => lang('apply_selected'), 'apply_all' => lang('apply_all')), 'apply_selected');

		$settings['cookies'] = array('t', array('rows' => '20'), '');

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
		if (isset($this->settings['cookies']) && ! empty($this->settings['cookies']))
		{
			$cookies = explode("\n", str_replace(",", "\n", trim($this->settings['cookies'])));
			$cookies = array_map('trim', $cookies);

			$cookieName = $data['prefix'].$data['name'];
			$data['samesite'] = (isset($this->settings['samesite']) ? $this->settings['samesite'] : '');
			
			if (isset($this->settings['all_cookies']) && $this->settings['all_cookies'] === 'apply_all')
			{
				$return = $this->set_samesite_cookie($data);
				ee()->extensions->end_script = TRUE;
			}
			else if (in_array($cookieName, $cookies))
			{
				$return = $this->set_samesite_cookie($data);
				ee()->extensions->end_script = TRUE;
			}
		}
		return $return;
	}
	
	private function set_samesite_cookie($data)
	{
		if (PHP_VERSION_ID < 70300) {
			// Older versions of PHP do not support an array as the 3rd parameter,
			// thus the SameSite setting must be hacked in with the path option.
			return setcookie($data['prefix'].$data['name'], $data['value'],
				$data['expire'],
				$data['path'] . '; SameSite=' . $data['samesite'],
				$data['domain'],
				$data['secure_cookie'],
				$data['httponly']
			);
		} else {
			return setcookie($data['prefix'].$data['name'], $data['value'], [
				'expires' => $data['expire'],
				'path' => $data['path'],
				'domain' => $data['domain'],
				'secure' => $data['secure_cookie'],
				'httponly' => $data['httponly'],
				'samesite' => $data['samesite'],
			]);
		}
		return FALSE;
	}
	
}