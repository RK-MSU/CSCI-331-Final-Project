<?php

namespace EllisLab\ExpressionEngine\Model\Site;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Stats Table
 *
 * @package		ExpressionEngine
 * @subpackage	Site
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Stats extends Model {

	protected static $_primary_key = 'stat_id';
	protected static $_table_name = 'stats';

	protected static $_relationships = array(
		'Site' => array(
			'type' => 'BelongsTo'
		),
		'RecentMember' => array(
			'type'     => 'BelongsTo',
			'model'    => 'Member',
			'from_key' => 'recent_member_id'
		)
	);

	protected $stat_id;
	protected $site_id;
	protected $total_members;
	protected $recent_member_id;
	protected $recent_member;
	protected $total_entries;
	protected $total_forum_topics;
	protected $total_forum_posts;
	protected $total_comments;
	protected $last_entry_date;
	protected $last_forum_post_date;
	protected $last_comment_date;
	protected $last_visitor_date;
	protected $most_visitors;
	protected $most_visitor_date;
	protected $last_cache_clear;

}