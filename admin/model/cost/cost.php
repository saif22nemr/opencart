<?php
class ModelCostCost extends Model {
	public function addCost($data){
		$val = $this->db->query("insert into ".DB_PREFIX."cost (description, cost_value, admin_id) values
		('".$this->db->escape($data['description'])."', ".(int)$data['value'].", ".(int)$data['admin'].")");
		return $val;
	}
	public function getCosts($data = array()){
		$sql = "select concat(u.firstname, ' ' ,u.lastname) as name, c.description, c.cost_id, c.cost_value, c.admin_id
		from ".DB_PREFIX."cost c inner join ".DB_PREFIX."user u on c.admin_id=u.user_id";
		$sort = array(
			'cost_value', 'cost_id'
		);
		$order = ['asc', 'desc'];

		if(isset($data['sort']) and in_array($data['sort'], $sort))
			$sql .= " order by ".$data['sort'];
		else $sql .= " order by cost_id";

		if(isset($data['order']) and in_array(strtolower($data['order']), $order))
			$sql .= " ".$data['order'];
		else $sql .= " asc";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function editCost($cost_id, $data = array()){
		$val = $this->db->query("update ".DB_PREFIX."cost set description='".$this->db->escape($data['description'])."' , cost_value=".(int)$data['value'].",admin_id=".(int)$data['admin']." where cost_id=".(int)$cost_id);
		return $val;
	}
	public function deleteCost($id=0){
		$val = $this->db->query('delete from '.DB_PREFIX.'cost where cost_id='.$id);
		return $val;
	}
	public function getTotalCosts(){
		$query = $this->db->query('select count(*) as c from '.DB_PREFIX.'cost');
		return $query->row['c'];
	}
	public function getCost($cost_id){
		$query = $this->db->query("select concat(u.firstname, ' ' ,u.lastname) as name, c.description, c.cost_id, c.cost_value, c.admin_id
		from ".DB_PREFIX."cost c inner join ".DB_PREFIX."user u on c.admin_id=u.user_id where cost_id = ".(int)$cost_id);
		return $query->row;
	}
	public function getCostDescriptions($id){
		$row = $this->getCost((int)$id);
		return $row['description'];
	}
	public function getCostName($id){
		$row = $this->getCost((int)$id);
		return $row['name'];
	}
	public function getCostAdminId($id){
		$row = $this->getCost((int)$id);
		return $row['admin_id'];
	}
	public function getCostValue($id){
		$row = $this->getCost((int)$id);
		return $row['cost_value'];
	}
}
