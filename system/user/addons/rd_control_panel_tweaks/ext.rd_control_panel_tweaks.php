<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RD Control Panel Tweaks extension class
 *
 * @package		RD Control Panel Styling
 * @author		Jason Boothman
 * @copyright	Copyright (c) 2017, Reusser Design
 * @link		http://reusserdesign.com
 * @since		1.0
 * @filesource 	./system/user/addons/rd_control_panel_tweaks/ext.rd_control_panel_tweaks.php
 */
class Rd_control_panel_tweaks_ext {

	var $settings = array();

	/**
	 * Constructor
	 *
	 */
	public function __construct($settings = '')
	{
		$this->settings = $settings;

		// required extension properties
		$this->name				= 'RD Control Panel Tweaks';
		$this->version			= '1.1.1';
		$this->description		= 'This add-on modifies some of the default control panel styling as well as gives you options to make various overrides such as button colors and hiding certain parts of the navigation. You can now customize control panel colors (within reason) to match your brand.';
		$this->settings_exist	= 'y';

		ee()->load->library('session');
	}

	// ------------------------------------------------------

	/**
	 * Activate Extension
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		 $this->_add_hook('cp_css_end', 10);
	}

	// ------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * @return void
	 */
	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	// ------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * @param 	string	String value of current version
	 * @return 	mixed	void on update / FALSE if none
	 */
	public function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE; // up to date
		}

		// update table row with current version
		ee()->db->where('class', __CLASS__);
		ee()->db->update('extensions', array('version' => $this->version));
	}

	function settings()
	{
		// Get all user groups that have access to the control panel
		$query = ee()->db->select('group_id, group_title')->get_where('member_groups', array('can_access_cp' => 'y'));

		$groups = array();

		foreach ($query->result() as $row)
		{
				$groups[$row->group_id] = $row->group_title;
		}

		$settings = array();

		// Color Settings
		$settings['color_top_bar']      		= array('i', '', '');
		$settings['color_top_bar_link']     	= array('i', '', '');
		$settings['color_top_bar_link_hover']	= array('i', '', '');
		$settings['color_link']      			= array('i', '', '');
		$settings['color_link_hover']      		= array('i', '', '');
		$settings['color_button']      			= array('i', '', '');
		$settings['color_button_hover']      	= array('i', '', '');
		$settings['color_button_action']     	= array('i', '', '');
		$settings['color_button_action_hover']	= array('i', '', '');

		// Settings for hiding buttons
		$settings['hide_files_button'] = array('ms', $groups, '');
		$settings['hide_developer_button'] = array('ms', $groups, '');
		$settings['hide_preview_button'] = array('ms', $groups, '');

		return $settings;
	}

	// ------------------------------------------------------
    //
    /**
     * Method for cp_css_end hook
     *
     * Add custom CSS to every Control Panel page:
     *
     * @access     public
     * @param      array
     * @return     array
     */
    public function cp_css_end()
    {
		//Set Defaults
		$css = '';
		$group_id = ee()->session->userdata('group_id');

		//Get Overrides from settings or set to EE 4 Defaults
		$color_top_bar = isset($this->settings['color_top_bar']) && $this->settings['color_top_bar'] != '' ? $this->settings['color_top_bar'] : '#333';
		$color_top_bar_link = isset($this->settings['color_top_bar_link']) && $this->settings['color_top_bar_link'] != '' ? $this->settings['color_top_bar_link'] : 'rgba(255,255,255,.8)';
		$color_top_bar_link_hover = isset($this->settings['color_top_bar_link_hover']) && $this->settings['color_top_bar_link_hover'] != '' ? $this->settings['color_top_bar_link_hover'] : '#fc0';
		$color_link = isset($this->settings['color_link']) && $this->settings['color_link'] != '' ? $this->settings['color_link'] : '#008da7';
		$color_link_hover = isset($this->settings['color_link_hover']) && $this->settings['color_link_hover'] != '' ? $this->settings['color_link_hover'] : '#009ae1';
		$color_button = isset($this->settings['color_button']) && $this->settings['color_button'] != '' ? $this->settings['color_button'] : '#01bf75';
		$color_button_hover = isset($this->settings['color_button_hover']) && $this->settings['color_button_hover'] != '' ? $this->settings['color_button_hover'] : '#01a665';
		$color_button_action = isset($this->settings['color_button_action']) && $this->settings['color_button_action'] != '' ? $this->settings['color_button_action'] : '#009ae1';
		$color_button_action_hover = isset($this->settings['color_button_action_hover']) && $this->settings['color_button_action_hover'] != '' ? $this->settings['color_button_action_hover'] : '#0089c8';

		//Bring in all our custom CSS files
		$cp_style = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/cp.css');
		$structure_style = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/structure.css');
		$assets_style = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/assets.css');
		$wygwam_style = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/wygwam.css');
		$hide_files_button = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/hide-files-button.css');
		$hide_developer_button = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/hide-developer-button.css');
		$hide_preview_button = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/hide-preview-button.css');
		$upper_nav = file_get_contents( PATH_THIRD . '/rd_control_panel_tweaks/css/upper-nav.css');

		//Hide buttons
		if (isset($this->settings['hide_files_button'])) {
			foreach ($this->settings['hide_files_button'] as $row)
			{
				if ($row == $group_id) {
					$css .= $hide_files_button;
				}
			}
		}

		if (isset($this->settings['hide_developer_button'])) {
			foreach ($this->settings['hide_developer_button'] as $row)
			{
				if ($row == $group_id) {
					$css .= $hide_developer_button;
				}
			}
		}

		if (isset($this->settings['hide_preview_button'])) {
			foreach ($this->settings['hide_preview_button'] as $row)
			{
				if ($row == $group_id) {
					$css .= $hide_preview_button;
				}
			}
		}

		$css .= $cp_style . $structure_style . $assets_style . $wygwam_style . $upper_nav;

		//Replace colors in CSS
		$css = str_replace('{{color_top_bar}}', $color_top_bar, $css);
		$css = str_replace('{{color_top_bar_link}}', $color_top_bar_link, $css);
		$css = str_replace('{{color_top_bar_link_hover}}', $color_top_bar_link_hover, $css);
		$css = str_replace('{{color_link}}', $color_link, $css);
		$css = str_replace('{{color_link_hover}}', $color_link_hover, $css);
		$css = str_replace('{{color_button}}', $color_button, $css);
		$css = str_replace('{{color_button_hover}}', $color_button_hover, $css);
		$css = str_replace('{{color_button_action}}', $color_button_action, $css);
		$css = str_replace('{{color_button_action_hover}}', $color_button_action_hover, $css);

		$other_css = [];

		//If another extension shares the same hook
		if (ee()->extensions->last_call !== false) {
			$other_css[] = ee()->extensions->last_call;
		}

    	return implode('', $other_css) . $css;
    }

	// --------------------------------------------------------------------

    /**
     * Add extension hook
     *
     * @access     private
     * @param      string
     * @param      integer
     * @return     void
     */
    private function _add_hook($name, $priority = 10)
    {
        ee()->db->insert('extensions',
            array(
                'class'    => __CLASS__,
                'method'   => $name,
                'hook'     => $name,
                'settings' => '',
                'priority' => $priority,
                'version'  => $this->version,
                'enabled'  => 'y'
            )
        );
	}
}