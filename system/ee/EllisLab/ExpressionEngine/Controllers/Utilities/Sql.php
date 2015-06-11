<?php

namespace EllisLab\ExpressionEngine\Controllers\Utilities;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP;

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
 * ExpressionEngine CP SQL Manager Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Sql extends Utilities {

	/**
	 * SQL Manager
	 */
	public function index()
	{
		if ( ! $this->cp->allowed_group('can_access_tools', 'can_access_data'))
		{
			show_error(lang('unauthorized_access'));
		}

		if (($action = ee()->input->post('table_action')) && ! ee()->input->post('search_form'))
		{
			$tables = ee()->input->post('table');

			// Must select an action
			if ($action == 'none')
			{
				ee()->view->set_message('issue', lang('cp_message_issue'), lang('no_action_selected'));
			}
			// Must be either OPTIMIZE or REPAIR
			elseif ( ! in_array($action, array('OPTIMIZE', 'REPAIR')))
			{
				show_error(lang('unauthorized_access'));
			}
			// Must have selected tables
			elseif (empty($tables))
			{
				ee()->view->set_message('issue', lang('cp_message_issue'), lang('no_tables_selected'));
			}
			else
			{
				return $this->opResults();
			}
		}

		ee()->load->model('tools_model');
		$vars = ee()->tools_model->get_sql_info();
		$vars += ee()->tools_model->get_table_status();

		foreach ($vars['status'] as $table)
		{
			$data[] = array(
				$table['name'],
				$table['rows'],
				$table['size'],
				array('toolbar_items' => array(
					'view' => array(
						'href' => cp_url(
							'utilities/query/run-query/'.$table['name'],
							array('thequery' => rawurlencode(base64_encode('SELECT * FROM '.$table['name'])))
						),
						'title' => lang('view')
					)
				)),
				array(
					'name' => 'table[]',
					'value' => $table['name']
				)
			);
		}

		$table = ee('CP/Table', array('autosort' => TRUE, 'autosearch' => TRUE));
		$table->setColumns(
			array(
				'table_name',
				'records',
				'size',
				'manage' => array(
					'type'	=> CP\Table::COL_TOOLBAR
				),
				array(
					'type'	=> CP\Table::COL_CHECKBOX
				)
			)
		);
		$table->setNoResultsText('no_tables_match');
		$table->setData($data);

		$base_url = new CP\URL('utilities/sql', ee()->session->session_id());
		$vars['table'] = $table->viewData($base_url);

		$pagination = new CP\Pagination(
			$vars['table']['limit'],
			$vars['table']['total_rows'],
			$vars['table']['page']
		);
		$vars['pagination'] = $pagination->cp_links($vars['table']['base_url']);

		ee()->view->cp_page_title = lang('sql_manager');
		ee()->view->table_heading = lang('database_tables');

		// Set search results heading
		if ( ! empty($vars['table']['search']))
		{
			ee()->view->table_heading = sprintf(
				lang('search_results_heading'),
				$vars['table']['total_rows'],
				$vars['table']['search']
			);
		}

		ee()->cp->render('utilities/sql/manager', $vars);
	}

	/**
	 * Results of table operation
	 */
	public function opResults()
	{
		$action = ee()->input->post('table_action');
		$tables = ee()->input->post('table');

		// This page can be invoked from a GET request due various ways to
		// sort and filter the table, so we need to check for cached data
		// from the original request
		if ($action == FALSE && $tables == FALSE)
		{
			$cache = ee()->cache->get('sql-op-results', \Cache::GLOBAL_SCOPE);

			if (empty($cache))
			{
				return $this->index();
			}
			else
			{
				$action = $cache['action'];
				$data = $cache['data'];
			}
		}
		else
		{
			// Perform the action on each selected table and store the results
			foreach ($tables as $table)
			{
				$query = ee()->db->query("{$action} TABLE ".ee()->db->escape_str($table));

				foreach ($query->result_array() as $row)
				{
					$row = array_values($row);
					$row[0] = $table;
					$data[] = array(
						$row[0],
						$row[2],
						$row[3]
					);
				}
			}

			$cache = array(
				'action' => $action,
				'data' => $data
			);

			// Cache it so we can access it on subsequent page requests due
			// to sorting and searching of the table
			ee()->cache->save('sql-op-results', $cache, 3600, \Cache::GLOBAL_SCOPE);
		}

		// Base URL for filtering
		$base_url = new CP\URL('utilities/sql/op-results', ee()->session->session_id());

		// Set up our table with automatic sorting and search capability
		$table = ee('CP/Table', array('autosort' => TRUE, 'autosearch' => TRUE));
		$table->setColumns(array(
			'table',
			'status' => array(
				'type' => CP\Table::COL_STATUS
			),
			'message'
		));
		$table->setData($data);
		$table->setNoResultsText('no_tables_match');
		$vars['table'] = $table->viewData($base_url);

		$pagination = new CP\Pagination(
			$vars['table']['limit'],
			$vars['table']['total_rows'],
			$vars['table']['page']
		);
		$vars['pagination'] = $pagination->cp_links($vars['table']['base_url']);

		ee()->view->cp_page_title = lang(strtolower($action).'_tables_results');
		ee()->cp->set_breadcrumb(cp_url('utilities/sql'), lang('sql_manager'));
		return ee()->cp->render('utilities/sql/ops', $vars);
	}
}
// END CLASS

/* End of file Query.php */
/* Location: ./system/EllisLab/ExpressionEngine/Controllers/Utilities/Query.php */
