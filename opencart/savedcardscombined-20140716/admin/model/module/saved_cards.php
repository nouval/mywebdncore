<?php
class ModelModuleSavedCards extends Model {
	
    public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "saved_cards` (
				`card_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`customer_id` INT(11) NOT NULL,
				`owner` VARCHAR(64) NOT NULL,
				`number` VARCHAR(64) NOT NULL,
				`month` VARCHAR(2) NOT NULL,
				`year` VARCHAR(4) NOT NULL,
				INDEX (`customer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "saved_cards`;");
    }

    public function purge() {
		$this->uninstall();
		$this->install();
    }

}