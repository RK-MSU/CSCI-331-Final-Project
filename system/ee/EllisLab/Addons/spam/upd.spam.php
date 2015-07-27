<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Spam Module
 *
 * @package		ExpressionEngine
 * @subpackage	Modules
 * @category	Modules
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */

class Spam_upd {

	public $version = '1.0.0';
	private $name = 'Spam';

	function Spam_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		ee()->load->dbforge();
	}

	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */
	function install()
	{
		$data = array(
			'module_name' => 'Spam' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		);

		ee()->db->insert('modules', $data);

		$fields = array(
			'kernel_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'name'		=> array('type' => 'varchar' , 'constraint' => '32'),
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('kernel_id', TRUE);
		ee()->dbforge->create_table('spam_kernels');

		$fields = array(
			'vocabulary_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'kernel_id'		=> array('type' => 'int', 'constraint' => '10'),
			'term'			=> array('type' => 'varchar' , 'constraint' => '32'),
			'count'			=> array('type' => 'int' , 'constraint' => '10')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('vocabulary_id', TRUE);
		ee()->dbforge->create_table('spam_vocabulary');

		$fields = array(
			'parameter_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'kernel_id'		=> array('type' => 'int', 'constraint' => '10'),
			'term'			=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
			'class'			=> array('type' => 'tinyint' , 'constraint' => '1'),
			'mean'			=> array('type' => 'decimal' , 'constraint' => '16,12'),
			'variance'		=> array('type' => 'decimal' , 'constraint' => '16,12')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('parameter_id', TRUE);
		ee()->dbforge->create_table('spam_parameters');

		$fields = array(
			'training_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'kernel_id'		=> array('type' => 'int', 'constraint' => '10'),
			'source'		=> array('type' => 'text'),
			'type'			=> array('type' => 'varchar', 'constraint' => '32'),
			'class'			=> array('type' => 'tinyint', 'constraint' => '1')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('training_id', TRUE);
		ee()->dbforge->create_table('spam_training');

		$fields = array(
			'trap_id'	 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'author'	 => array('type' => 'int', 'constraint' => '10'),
			'ip_address' => array('type' => 'varchar', 'constraint' => '45'),
			'date'	     => array('type' => 'datetime'),
			'file'		 => array('type' => 'varchar', 'constraint' => '129'),
			'class'		 => array('type' => 'varchar', 'constraint' => '64'),
			'method'	 => array('type' => 'varchar', 'constraint' => '64'),
			'data'		 => array('type' => 'text'),
			'document'	 => array('type' => 'text')
		);

		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('trap_id', TRUE);
		ee()->dbforge->create_table('spam_trap');

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{
		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' => 'Spam'));

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		ee()->db->where('module_name', 'Spam');
		ee()->db->delete('modules');

		ee()->db->where('class', 'Spam');
		ee()->db->delete('actions');

		ee()->db->where('class', 'Spam_mcp');
		ee()->db->delete('actions');

		ee()->dbforge->drop_table('spam_vocabulary');
		ee()->dbforge->drop_table('spam_parameters');
		ee()->dbforge->drop_table('spam_training');
		ee()->dbforge->drop_table('spam_trap');

		return TRUE;
	}

	function update($current='')
	{
		return TRUE;
	}

}
