<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2018, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\Addons\Comment\Service\Variables;

use EllisLab\ExpressionEngine\Model\Comment\Comment as CommentModel;
use EllisLab\ExpressionEngine\Service\Template\Variables;

/**
 * Comment Variables
 */
class Comment extends Variables {

	/**
	 * @var object namespace EllisLab\ExpressionEngine\Model\Member\Member
	 */
	private $author;

	/**
	 * @var object EllisLab\ExpressionEngine\Model\Channel\Channel
	 */
	private $channel;

	/**
	 * @var object EllisLab\ExpressionEngine\Model\Comment\Comment
	 */
	private $comment;

	/**
	 * @var object EllisLab\ExpressionEngine\Model\Channel\ChannelEntry
	 */
	private $entry;

	/**
	 * @var string A pre-parsed ACTion URL for member search
	 */
	private $member_search_url;

	/**
	 * Constructor
	 *
	 * @param object $comment EllisLab\ExpressionEngine\Model\Comment\Comment
	 * @param string $member_search_url A pre-parsed ACTion URL for member search
	 */
	public function __construct(CommentModel $comment, $member_search_url)
	{
		$this->author = ($comment->Author) ?: ee('Model')->make('Member');
		$this->channel = $comment->Channel;
		$this->comment = $comment;
		$this->entry = $comment->Entry;
		$this->member_search_url = $member_search_url;

		parent::__construct();
	}

	/**
	 * getTemplateVariables
	 * @return array fully prepped variables to be parsed
	 */
	public function getTemplateVariables()
	{
		if ( ! empty($this->variables))
		{
			return $this->variables;
		}

		$typography_prefs = [
			'text_format'	=> $this->channel->comment_text_formatting,
			'html_format'	=> $this->channel->comment_html_formatting,
			'auto_links'	=> $this->channel->comment_auto_link_urls,
			'allow_img_url' => $this->channel->comment_allow_img_urls,
		];

		$base_url = ($this->channel->comment_url) ?: $this->channel->channel_url;
		$base_url = parse_config_variables($base_url, ee()->config->get_cached_site_prefs($this->comment->site_id));

		$this->variables = [
			'author'                     => ($this->author->screen_name) ?: $this->comment->name,
			'author_id'                  => $this->comment->author_id,
			'channel_id'                 => $this->entry->channel_id,
			'channel_short_name'         => $this->channel->channel_name,
			'channel_title'              => $this->channel->channel_title,
			'comment'                    => $this->typography($this->comment->comment, $typography_prefs),
			'comment_auto_path'          => $base_url,
			'comment_date'               => $this->date($this->comment->comment_date),
			'comment_entry_id_auto_path' => $base_url.'/'.$this->comment->entry_id,
			'comment_expiration_date'    => $this->date($this->entry->comment_expiration_date),
			'comment_id'                 => $this->comment->comment_id,
			'comment_site_id'            => $this->comment->site_id,
			'comment_stripped'           => $this->protect($this->comment->comment),
			'comment_url_title_auto_path' => $base_url.'/'.$this->entry->url_title,
			'comments_disabled'          => $this->isDisabled(),
			'comments_expired'           => (ee()->localize->now > $this->entry->comment_expiration_date),
			'editable'                   => $this->isEditable(),
			'edit_date'                  => $this->date($this->comment->edit_date),
			'email'                      => $this->comment->email,
			'entry_author_id'            => $this->entry->author_id,
			'entry_id'                   => $this->comment->entry_id,
			'entry_id_path'              => $this->pathVariable($this->comment->entry_id),
		//	'gmt_comment_date'           => $this->date($this->comment->comment_date), // eliminated, just use timestamp= with comment date
			'group_id'                   => $this->author->group_id,
			'ip_address'                 => $this->comment->ip_address,
			'location'                   => ($this->comment->location) ?: $this->author->location,
			'member_search_path'         => $this->member_search_path . $this->comment->author_id,
			'name'                       => $this->comment->name,
			'status'                     => $this->comment->status,
			'title'                      => $this->entry->title,
			'url'                        => ($this->comment->url) ?: $this->author->url,
			'url_title'                  => $this->entry->url_title,
			'url_title_path'             => $this->pathVariable($this->entry->url_title),
		];

		return $this->variables;
	}

	private function isDisabled()
	{
		return ($this->entry->allow_comments === FALSE OR
			$this->channel->comment_system_enabled === FALSE OR
			bool_config_item('enable_comments') === FALSE);
	}

	private function isEditable()
	{
		if (ee('Permission')->has('can_edit_all_comments'))
		{
			return TRUE;
		}

		if ($this->ee('Permission')->has('can_edit_own_comments') &&
			$this->entry->author_id == ee()->session->userdata('member_id'))
		{
			return TRUE;
		}

		if ($this->comment->author_id == ee()->session->userdata('member_id'))
		{
			if (ee()->config->item('comment_edit_time_limit') == 0)
			{
				return TRUE;
			}

			$time_limit_sec = 60 * ee()->config->item('comment_edit_time_limit');
			return $this->comment->comment_date > (ee()->localize->now - $time_limit_sec);
		}

		return FALSE;
	}
}
// END CLASS

// EOF