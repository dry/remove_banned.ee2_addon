<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package		Remove Banned	
 * @author		Greg Salt <greg@purple-dogfish.co.uk>	
 * @copyright	Copyright (c) 2012, Purple Dogfish Ltd.
 * @license		MIT
 * @link		http://www.purple-dogfish.co.uk
 * @since		Version 1.0
 */
class Remove_banned_ext {
	
	/**
	 * @access	public
	 * @var		array
	 */
	public $settings = array();

	/**
	 * @access	public
	 * @var		string
	 */
	public $description = 'Removed banned members from the View Members page';

	/**
	 * @access	public
	 * @var		string
	 */
	public $docs_url = 'https://github.com/dry/remove_banned.ee2_addon';

	/**
	 * @access	public
	 * @var		string
	 */
	public $name = 'Remove Banned';

	/**
	 * @access	public
	 * @var		string
	 */
	public $settings_exist = 'n';

	/**
	 * @access	public
	 * @var		string
	 */
	public $version = '1.0';

	/**
	 * @access	private
	 * @var		$EE		ExpressionEngine
	 */
	private $EE;


	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->EE->lang->loadfile('remove_banned');
		$this->description = lang('remove_banned_description');
		$this->name = lang('remove_banned_name');

		$this->settings = $settings;
	}

    /**
     * Activate Extension
     *
     * @access  public
     * @return  void
     */
    public function activate_extension()
	{
		$hooks = array(
			'sessions_end' => 'sessions_end',
		);

		foreach ($hooks as $hook => $method)
		{
			$data = array(
				'class' => __CLASS__,
				'method' => $method,
				'hook' => $hook,
				'settings' => serialize($this->settings),
				'version' => $this->version,
				'enabled' => 'y',
				'priority' => 1
			);

			$this->EE->db->insert('extensions', $data);			
		}
	}	

	/**
	 * Sessions End	
	 *
	 * @param	$session	Session
	 * return	void
	 */
	public function sessions_end(&$session)
	{
		if (REQ === 'CP' AND $this->EE->input->get('M') == 'view_all_members')
		{
			// At this point we overload the standard member model by
			// loading the original model, requiring our package model
			// that extends the original and then replacing the object
			// I'm sure that Pascal Kriete would slap me around the head
			// for this but...it has to be done :)
			$this->EE->load->model('member_model');
			require_once PATH_THIRD.'remove_banned/models/overload_member_model.php';
			$this->EE->member_model = new Overload_member_model;
		}
	}

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	public function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}
}
/* End of file ext.remove_banned.php */
/* Location: /system/expressionengine/third_party/remove_banned/ext.remove_banned.php */
