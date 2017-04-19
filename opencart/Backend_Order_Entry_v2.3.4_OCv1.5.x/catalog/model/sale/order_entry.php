<?php

class ModelSaleOrderEntry extends Model {

	public function getProductName($product_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['name'];
	}

	public function getModelNumber($product_id) {
		$query = $this->db->query("SELECT model FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['model'];
	}

	public function getProductQuantity($product_id) {
		$query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['quantity'];
	}

	public function getSku($product_id) {
		$query = $this->db->query("SELECT sku FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['sku'];
	}

	public function getUpc($product_id) {
		$query = $this->db->query("SELECT upc FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['upc'];
	}

	public function getWeight($product_id) {
		$query = $this->db->query("SELECT weight, weight_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row;
	}

	public function getLocation($product_id) {
		$query = $this->db->query("SELECT location FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		return $query->row['location'];
	}

	public function getTaxClassId() {
		$query = $this->db->query("SELECT DISTINCT(tax_class_id) FROM " . DB_PREFIX . "product");
		if ($query->num_rows) {
			if (count($query->rows) == 1) {
				return $query->row['tax_class_id'];
			} else {
				foreach ($query->rows as $row) {
					if ($row['tax_class_id'] > 0) {
						return $row['tax_class_id'];
					}
				}
			}
		} else {
			return 0;
		}
	}
	
}

?>