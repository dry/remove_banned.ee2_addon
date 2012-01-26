<?php if ( ! defined('BASEPATH')) exit('Direct script access not allowed');

class Overload_member_model extends Member_model {

	/**
	 * @access	private
	 * @var		$EE		ExpressionEngine
	 */
	private $EE;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	/**
	 * Get Members
	 *
	 * This is a modified copy of the EE member_model::get_members method
	 *
	 * @access	public
	 * @return	mixed	FALSE if no members or array
	 */	
	public function get_members($group_id = '', $limit = '', $offset = '', $search_value = '', $order = array(), $column = 'all')
	{
		// We check to see whether the banned group has been requested and if not we just
		// remove them from the query 
		if ($this->EE->input->post('group_id') != '2')
		{
			$this->EE->db->where('group_id !=', '2');
		}

		// All below this line is the original method
		$this->db->select("members.username, members.member_id, members.screen_name, members.email, members.join_date, members.last_visit, members.group_id, members.member_id, members.in_authorlist");

		$this->_prep_search_query($group_id, $search_value, $column);

		if ($limit != '')
		{
			$this->db->limit($limit);
		}

		if ($offset != '')
		{
			$this->db->offset($offset);
		}

		if (is_array($order) && count($order) > 0)
		{
			foreach ($order as $key => $val)
			{
				$this->db->order_by($key, $val);
			}
		}
		else
		{
			$this->db->order_by('join_date');
		}

		$members = $this->db->get('members');

		if ($members->num_rows() == 0)
		{
			return FALSE;
		}
		else
		{
			return $members;
		}
	}

	/**
	 * Set up the search query which is used by get_members and
	 * count_members. Be sure to *run* the query after calling this.
	 *
	 * This is a copy of the original member_model method. We need this here
	 * because it is a private method and so can't be called on the parent
	 *
	 * @access	private
	 * @param	int
	 * @return	int
	 */
	private function _prep_search_query($group_id = '', $search_value = '', $search_in = '')
	{
		$no_search = array('password', 'salt', 'crypt_key');

		if ($group_id !== '')
		{
			$this->db->where("members.group_id", $group_id);
		}

		if (is_array($search_value))
		{
			foreach ($search_value as $token_name => $token_value)
			{
				// Check to see if the token is ID
				$token_name = ($token_name === 'id') ? 'member_id' : $token_name;

				$this->db->like('members.'.$token_name, $token_value);
			}
		}
		elseif ($search_value != '')
		{
			$search_field = 'all';

			if ( ! in_array($search_in, $no_search))
			{
				$search_in = $search_field;
			}

			if ($search_in == 'all')
			{
				$this->db->where("(`exp_members`.`screen_name` LIKE '%".$this->db->escape_like_str($search_value)."%' OR `exp_members`.`username` LIKE '%".$this->db->escape_like_str($search_value)."%' OR `exp_members`.`email` LIKE '%".$this->db->escape_like_str($search_value)."%')", NULL, TRUE);			
			}
			else
			{			
				$this->db->like('members.'.$search_in, $search_value);
			}
		}
	}
}
/* End of file overload_member_model.php */
/* Location: ./system/expressionengine/third_party/remove_banned/models/overload_member_model.php */
