<?php

class ModelModuleOrderEntry extends Model {

	public function getCustomerOpenOrders($customer_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order`";
		if ($this->config->get('order_history_open_statuses')) {
			foreach ($this->config->get('order_history_open_statuses') as $order_status_id) {
				if (isset($where)) {
					$where .= " OR order_status_id = '" . (int)$order_status_id . "'";
				} else {
					$where = " WHERE (order_status_id = '" . (int)$order_status_id . "'";
				}
			}
			if ($this->config->get('order_history_open_number')) {
				$limit = $this->config->get('order_history_open_number');
			} else {
				$limit = 10;
			}
			$order_by = ") AND customer_id = " . (int)$customer_id . " ORDER BY date_added DESC LIMIT 0," . $limit;
			$query = $this->db->query($sql . $where . $order_by);
			return $query->rows;
		}
		return;
	}
	
	public function getCustomerPendingOrders($customer_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order`";
		if ($this->config->get('order_history_pending_statuses')) {
			foreach ($this->config->get('order_history_pending_statuses') as $order_status_id) {
				if (isset($where)) {
					$where .= " OR order_status_id = '" . (int)$order_status_id . "'";
				} else {
					$where = " WHERE (order_status_id = '" . (int)$order_status_id . "'";
				}
			}
			if ($this->config->get('order_history_pending_number')) {
				$limit = $this->config->get('order_history_pending_number');
			} else {
				$limit = 5;
			}
			$order_by = ") AND customer_id = " . (int)$customer_id . " ORDER BY date_added DESC LIMIT 0," . $limit;
			$query = $this->db->query($sql . $where . $order_by);
			return $query->rows;
		}
		return;
	}
	
	public function getCustomerClosedOrders($customer_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order`";
		if ($this->config->get('order_history_closed_statuses')) {
			foreach ($this->config->get('order_history_closed_statuses') as $order_status_id) {
				if (isset($where)) {
					$where .= " OR order_status_id = '" . (int)$order_status_id . "'";
				} else {
					$where = " WHERE (order_status_id = '" . (int)$order_status_id . "'";
				}
			}
			if ($this->config->get('order_history_closed_number')) {
				$limit = $this->config->get('order_history_closed_number');
			} else {
				$limit = 5;
			}
			$order_by = ") AND customer_id = " . (int)$customer_id . " ORDER BY date_added DESC LIMIT 0," . $limit;
			$query = $this->db->query($sql . $where . $order_by);
			return $query->rows;
		}
		return;
	}
	
	public function getOrderStatus($order_status_id) {
		$order_status_name = "";
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");
		if ($query->num_rows) {
			$order_status_name = $query->row['name'];
		}
		return $order_status_name;
	}
	
	public function getBalance($order_id) {
		$balance = 0;
		$query = $this->db->query("SELECT balance FROM " . DB_PREFIX . "installments_orders WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows) {
			$balance = $query->row['balance'];
		} else {
			$order_total = $this->db->query("SELECT total FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
			$query = $this->db->query("SELECT deposit,payments FROM " . DB_PREFIX . "order_layaway WHERE order_id = '" . (int)$order_id . "'");
			if ($query->num_rows) {
				$balance = $order_total->row['total'];
				$balance -= $query->row['deposit'];
				if (!empty($query->row['payments'])) {
					foreach (unserialize($query->row['payments']) as $payment) {
						$balance -= $payment['payment_amount'];
					}
				}
			}
		}
		return $balance;
	}
	
}

?>