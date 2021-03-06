<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Place for generic database function all around the model class.
 * @copyright PT. Badr Interactive (c) 2014
 * @author pulung
 */
class Generic_Model extends CI_Model {
	
	
	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->load->database();
		
	}
	
	/**
	 * Create new element in $table with $configuration parameter.
	 * @param string $table table name
	 * @param array $configuration array contains row data to be inserted in $table
	 * @return number just created row ID.
	 */
	public function create($table="", $configuration=array()) {
		
		// check if table exist in db.
		if (!$this->db->table_exists($table)) {
			return -1;
		}
		
		$type = unserialize(DATETIME_TYPE_KEYS);
		foreach($configuration as $key => $item) {
			if(in_array($key, $type)) {
				$date = new DateTime($configuration[$key]);
				$date->add(new DateInterval('P'
						. ADDITIONAL_YEARS . 'Y'
						. ADDITIONAL_MONTHS . 'M'
						. ADDITIONAL_DATES . 'DT'
						. ADDITIONAL_HOURS . 'H'
						. ADDITIONAL_MINUTES . 'M'
						. ADDITIONAL_SECONDS . 'S'));
				$configuration[$key] = $date->format('Y-m-d H:i:s');
			}
		}
		
		$this->db->insert($table, $configuration);
		// return ID generated by auto-increment last insert.
		return $this->db->insert_id();
	}
	
	/**
	 * Retrieve $table data by given $criteria.
	 * @param $table table name
	 * @param $criteria criteria for retrieving data.
	 * @return result in one row in one dimensional array.
	 */
	public function retrieve_one($table="", $criteria=array(), $order_criteria=array(), $selected="*") {
		
		// check if table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->select($selected);
		$this->db->where($criteria);
		
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		$query = $this->db->get($table);
		return $query->row_array();
	}
	
	/**
	 * Retrieve rows from given parameter.
	 * @param string $table table name
	 * @param array $criteria criteria for retrieving data.
	 * @param array $order_criteria array of field => order (ascending or descending)
	 * @param string $length length of row retrieved.
	 * @param string $offset retrieval starting point.
	 * @return result in several row in two dimensional array.
	 */
	public function retrieve_many($table="", $criteria=array(), 
			$order_criteria=array(), $length=NULL, $offset=NULL) {
	
		// check if table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->where($criteria);
		
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		if ($length && $offset) {
			$query = $this->db->get($table, $length, $offset);
		} else {
			$query = $this->db->get($table);
		}
		
		return $query->result_array();
	}
	
	/**
	 * Retrieve rows with selected fields from given parameter.
	 * @param string $table table name
	 * @param array $criteria criteria for retrieving data.
	 * @param string $selected selected field, separated by comma ','
	 * @param array $order_criteria array of field => order (ascending or descending)
	 * @param string $length length of row retrieved.
	 * @param string $offset retrieval starting point.
	 * @return result in several row in two dimensional array.
	 */
	public function retrieve_many_with_selection($table="", $criteria=array(),
			$selected="*", $order_criteria=array(), $length=NULL, $offset=NULL) {
		
		// check if table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->select($selected); // select the fields
		$this->db->where($criteria); // set the criteria for rows.
		
		// set the order (asc or desc) for retrieval
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		// arrange the length of retrieved row and where to start retrieve it.
		if ($length && $offset) {
			$query = $this->db->get($table, $length, $offset);
		} else {
			$query = $this->db->get($table);
		}
	
		return $query->result_array();
	}
	
	/**
	 * Retrieve rows by list of data using "where in" operation.
	 * @param string $table table name
	 * @param string $field_name field name
	 * @param array $data_list list of data in field name
	 * @param array $order_criteria data order
	 * @param string $length length of data
	 * @param string $offset offset of data
	 */
	public function retrieve_many_in_list($table="", $field_name="id", $data_list=array(), 
			$order_criteria=array(), $length=NULL, $offset=NULL) {
		
		// check if table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->where_in($field_name, $data_list);
		
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		if ($length && $offset) {
			$query = $this->db->get($table, $length, $offset);
		} else {
			$query = $this->db->get($table);
		}
		
		return $query->result_array();
	}
	
	
	/**
	 * Update data in a table named $table, 
	 * with update data $configuration, with $where_criteria for where operation.
	 * @param string $table table name
	 * @param unknown $configuration things to be updated.
	 * @param unknown $where_criteria where operation criteria.
	 */
	public function update($table="", $configuration=array(), $where_criteria=array()) {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$type = unserialize(DATETIME_TYPE_KEYS);
		foreach($configuration as $key => $item) {
			if(in_array($key, $type)) {
				$date = new DateTime($configuration[$key]);
				$date->add(new DateInterval('P'
						. ADDITIONAL_YEARS . 'Y'
						. ADDITIONAL_MONTHS . 'M'
						. ADDITIONAL_DATES . 'DT'
						. ADDITIONAL_HOURS . 'H'
						. ADDITIONAL_MINUTES . 'M'
						. ADDITIONAL_SECONDS . 'S'));
				$configuration[$key] = $date->format('Y-m-d H:i:s');
			}
		}
		
		$this->db->where($where_criteria);
		$this->db->update($table, $configuration);
	}
	
	/**
	 * Update the table with transaction capabilities.
	 * @param string $table table name.
	 * @param array $configuration configuration updated field.
	 * @param array $where_criteria criteria for where operation.
	 */
	public function update_with_transaction($table="", 
			$configuration=array(), $where_criteria=array()) {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$type = unserialize(DATETIME_TYPE_KEYS);
		foreach($configuration as $key => $item) {
			if(in_array($key, $type)) {
				$date = new DateTime($configuration[$key]);
				$date->add(new DateInterval('P'
						. ADDITIONAL_YEARS . 'Y'
						. ADDITIONAL_MONTHS . 'M'
						. ADDITIONAL_DATES . 'DT'
						. ADDITIONAL_HOURS . 'H'
						. ADDITIONAL_MINUTES . 'M'
						. ADDITIONAL_SECONDS . 'S'));
				$configuration[$key] = $date->format('Y-m-d H:i:s');
			}
		}
		
		// prepare the select statement + FOR UPDATE
		$select_string = "SELECT ";
		
		$counter = 0;
		foreach($configuration as $key => $value) {
			$select_string .= $this->db->escape($key);
			
			// separate selected field with commas except the last one.
			if ($counter < count($configuration) - 1) {
				$select_string .= ", ";
			}
			$counter++;
		}
		
		$select_string .= " FOR UPDATE;";
		
		// prepare the update string.
		$update_string = $this->db->update_string($table, $configuration, $where_criteria);
		
		$this->db->trans_start();
		
		$this->db->query($select_string);
		$this->db->query($update_string);
		
		$this->db->trans_complete();
	}
	
	/**
	 * Delete element from $table according to its $id.
	 * @param $table table name
	 * @param $id element ID
	 */
	public function delete_one($table="", $where_criteria) {
		
		$this->delete($table, $where_criteria);
	}
	
	/**
	 * Delete element from $table according to its $id.
	 * @param $table table name
	 * @param $where_criteria element criteria.
	 */
	public function delete($table="", $where_criteria) {
	
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
	
		$this->db->where($where_criteria);
		$this->db->delete($table);
	}
	
	/**
	 * Delete many element from checkboxed row in list
	 * @param $table table name
	 * @param $ids element-element ID
	 */
	public function delete_many($table="", $ids=array(), $reference_field="id") {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->where_in($reference_field, $ids);
		$this->db->delete($table);
	}
	
	/**
	 * Search element of $table using $criteria, by LIKE operator.
	 * @param string $table table name.
	 * @param array $criteria list of criteria.
	 */
	public function search($table="", $like_criteria=array(), $where_criteria=array(),
			$order_criteria=array(), $length=NULL, $offset=NULL, $selected="*") {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return NULL;
		}
		
		$this->db->select($selected);
		
		$this->db->like($like_criteria);
		$this->db->where($where_criteria);
		
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		if ($length && $offset) {
			$query = $this->db->get($table, $length, $offset);
		} else {
			$query = $this->db->get($table);
		}
		
		return $query->result_array();
	}
	
	
	/**
	 * Get total rows from given query.
	 * @param string $table table name.
	 * @param array $where_criteria where criteria.
	 * @param array $like_criteria like criteria.
	 */
	public function get_total_rows($table="", $where_criteria=array(), 
			$like_criteria=array()) {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return -1;
		}
		
		$this->db->where($where_criteria);
		$this->db->like($like_criteria);
		$this->db->from($table);
		
		return $this->db->count_all_results();
	}
	
	public function sum_of_column($table="", $criteria=array(),
			$column_name="") {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($table)) {
			return -1;
		}
		
		$this->db->select("SUM(". $column_name .") AS ". $column_name ."_total");
		$this->db->where($criteria);
		$query = $this->db->get($table);
	
		return $query->row_array();
	}

}

	/**
	 * 
	 * @param string $primary_table primary table name to be joined.
	 * @param array $criteria criteria for primary table
	 * @param array $table_refid array (foreign table name => reference ID  on foreign table)
	 * @param string $selected selected field from joined table, concat in a string, separated with comma.
	 * @param array $alias_in_primary alias name for foreign field in primary table.
	 * @param array $order_criteria data order
	 * @param string $length length of data
	 * @param string $offset offset of data
	 */
	public function retrieve_joined($primary_table="", $criteria=array(), 
			$table_refid=array(), $selected="", $alias_in_primary=array(), 
			$order_criteria=array(), $length=NULL, $offset=NULL, $group_by="", $join_type="left") {
		
		// check if primary table exist in db.
		if (!$this->db->table_exists($primary_table)) {
			return NULL;
		}
		
		$this->db->select($selected);
		$this->db->from($primary_table);
		$this->db->where($criteria);
		
		foreach($table_refid as $foreign_table => $reference_id) {
			
			// check if foreign table exist in db.
			if (!$this->db->table_exists($foreign_table)) {
				continue; // continue joining another table.
			}
			
			// if $foreign table index hasn't SET at all, and if that index is SET to null
			if (isset($alias_in_primary[$foreign_table]) && $alias_in_primary[$foreign_table]) {
				
				$this->db->join($foreign_table, $foreign_table .".". $reference_id ."=".
						$primary_table .".". $alias_in_primary[$foreign_table], $join_type);
				
			} else {
				
				$this->db->join($foreign_table, $foreign_table .".". $reference_id ."=".
						$primary_table .".". $foreign_table ."_". $reference_id, $join_type);
			}
			
		}
		
		foreach($order_criteria as $field => $order) {
			$this->db->order_by($field, $order);
		}
		
		if($group_by != "") {
			$this->db->group_by($group_by);
		}
		
		if ($length && $offset) {
			$this->db->limit($length, $offset);
		} 
		
		$query = $this->db->get();
		return $query->result_array();
	}
	