<?php
class ModelAccountSavedCards extends Model {
	private $custom_encryption;
	
	public function __construct($params) {
		parent::__construct($params);
		$this->custom_encryption = new Encryption($this->config->get('saved_cards_encryption'));
	}
	
	public function addCard($data) {
		$data['number'] = $this->custom_encryption->encrypt(preg_replace('/\D/', '', $data['number']));
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "saved_cards SET
			customer_id = '" . (int)$this->customer->getId() . "',
			owner = '" . $this->db->escape($data['owner']) . "',
			number = '" . $this->db->escape($data['number']) . "',
			month = '" . $this->db->escape($data['month']) . "',
			year = '" . $this->db->escape($data['year']) . "'
		");
		return $this->db->getLastId();
	}
	
	public function editCard($card_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "saved_cards SET
			owner = '" . $this->db->escape($data['owner']) . "',
			month = '" . $this->db->escape($data['month']) . "',
			year = '" . $this->db->escape($data['year']) . "'
			WHERE card_id  = '" . (int)$card_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}
	
	public function deleteCard($card_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "saved_cards
			WHERE card_id = '" . (int)$card_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}	
	
	public function getCard($card_id) {
		$query = $this->db->query("
			SELECT DISTINCT * FROM " . DB_PREFIX . "saved_cards
			WHERE card_id = '" . (int)$card_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'
		");
		if ($query->num_rows) {
			$number = $this->custom_encryption->decrypt($query->row['number']);
			return array(
				'card_id' => $query->row['card_id'],
				'owner'   => $query->row['owner'],
				'number'  => $number,
				'masked'  => $this->maskNumber($number),
				'month'   => $query->row['month'],
				'year'    => $query->row['year']
			);
		} else {
			return false;	
		}
	}
	
	public function getCards() {
		$card_data = array();
		$query = $this->db->query("
			SELECT * FROM " . DB_PREFIX . "saved_cards
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");
		foreach ($query->rows as $result) {
			$number = $this->custom_encryption->decrypt($result['number']);
			$card_data[$result['card_id']] = array(
				'card_id' => $result['card_id'],
				'owner'   => $result['owner'],
				'number'  => $number,
				'masked'  => $this->maskNumber($number),
				'month'   => $result['month'],
				'year'    => $result['year']
			);
		}		
		return $card_data;
	}	
	
	public function getTotalCards() {
		$query = $this->db->query("
			SELECT COUNT(*) AS total FROM " . DB_PREFIX . "saved_cards
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");
		return $query->row['total'];
	}

	private function maskNumber($number) {
		return substr($number, 0, 2) . str_repeat('*', strlen($number) - 6) . substr($number, -4);
	}
		
}