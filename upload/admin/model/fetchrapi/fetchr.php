<?php
/*
  @Model: Fetchr Api Model.
  @Author: Moath Mobarak.
  @Version: 1.0.0
*/
class ModelFetchrapiFetchr extends Model {

	public function install()
	{
		$this->db->query("

	      CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fetchr_api_config` (
	      `key_config` varchar(40) NOT NULL,
	      `value` varchar(40) NOT NULL,
	       PRIMARY KEY (`key_config`)
	       ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
	    ");

	    $exist = $this->db->query("
	        SELECT * FROM `" . DB_PREFIX  . "order_status` WHERE name IN ('Fetchr Shipping', 'Ready for Pick up');
	    ");
	    
	    if ($exist->num_rows == 0) {
	      $this->db->query("
	       INSERT INTO `" . DB_PREFIX . "order_status` (`language_id`, `name`)
	       VALUES
	       ('1', 'Fetchr Shipping'),
	       ('1', 'Delivery Scheduled'),
	       ('1', 'Scheduled for Delivery'),
	       ('1', 'Held at Fetchr'),
	       ('1', 'In Transit'),
	       ('1', 'Ready for Pick up'),
	       ('1', 'Delivered');
	      ");
	    }

	}

    public function getConfig() {

		$sql = "SELECT * FROM `" . DB_PREFIX . "fetchr_api_config`";
        $query = $this->db->query($sql);

        return $query->rows;
	}

	public function saveConfig($data)
	{		
		$delete = $this->db->query("DELETE FROM " . DB_PREFIX . "fetchr_api_config");
		
		foreach ($data as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "fetchr_api_config SET key_config = '" . $this->db->escape($key) . "', value = '" . $this->db->escape($value) . "'");
		}
	}

	public function getOrders() 
	{
        $query = $this->db->query("SELECT order_id, store_id, firstname, lastname, email, telephone, payment_address_1, payment_city, total, payment_method, payment_code, comment, customer_id FROM `" . DB_PREFIX . "order` 
        	LEFT JOIN oc_order_status ON oc_order.order_status_id = oc_order_status.order_status_id
			WHERE oc_order_status.`name` = 'Ready for Pick up'");

        return $query->rows;
    }

    public function getOrderProducts($order_id) 
    {
        $query = $this->db->query("SELECT  op.order_id, op.name, op.quantity, p.sku, p.price FROM " . DB_PREFIX . "order_product op INNER JOIN " . DB_PREFIX . "product p on op.product_id=p.product_id  WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
	}

	public function getSetting($code, $store_id = 0) 
	{
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = $result['value'];
			}
		}

		return $setting_data;
	}

	public function saveTrackingStatus($orderId, $tracking_number)
	{
		//Get status.
		$Fetchr_Shipping = $this->db->query("SELECT order_status_id FROM oc_order_status WHERE name = 'Fetchr Shipping'");
		$Ready_Pick_up = $this->db->query("SELECT order_status_id FROM oc_order_status WHERE name = 'Ready for Pick up'");
		//Save tracking and change status.
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "order` SET tracking = '" . $this->db->escape($tracking_number) . "', order_status_id = '" . (int)$Fetchr_Shipping->row['order_status_id'] . "' WHERE order_id='" . (int)$orderId . "'");
		//Get taking number.
		$tracking_no = $this->db->query("SELECT tracking FROM " . DB_PREFIX . "order WHERE order_id='" . (int)$orderId ."'");

		if ($tracking_no->row['tracking']) {
			$href = "http://track.menavip.com/track.php?tracking_number=". $tracking_no->row['tracking'];
			$tracking_url = '<strong>Tracking URL:</strong> <a href="'. $href .'" target="_blank">' . $href . '</a>';
        	
			$histroy_data = $this->db->query("SELECT order_status_id, order_history_id, `comment` FROM oc_order_history WHERE order_id='" . (int)$orderId . "' AND order_status_id= '" . (int)$Ready_Pick_up->row['order_status_id'] . "' AND comment NOT LIKE '%track.menavip.com%'");

        	$history = $this->db->query("UPDATE `" . DB_PREFIX . "order_history` SET comment = '" . $this->db->escape($tracking_url) . '</br>' . $histroy_data->row['comment'] . "' WHERE order_id='" . (int)$orderId . "' AND order_status_id='" . (int)$Ready_Pick_up->row['order_status_id'] . "' AND order_history_id='" . $histroy_data->row['order_history_id'] . "'");
		}

		return $query;
	}

}