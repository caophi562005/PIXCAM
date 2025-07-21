<?php
class RevenueModel {
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getDaily() {
        $sql = "
            SELECT DATE(createAt) AS `day`,
                   SUM(finalTotal) AS `total`
              FROM payment
          GROUP BY `day`
          ORDER BY `day`
        ";
        return $this->db->prepareSelect($sql);
    }

    public function getMonthly() {
        $sql = "
            SELECT YEAR(createAt)  AS `year`,
                   MONTH(createAt) AS `month`,
                   SUM(finalTotal)  AS `total`
              FROM payment
          GROUP BY `year`, `month`
          ORDER BY `year`, `month`
        ";
        return $this->db->prepareSelect($sql);
    }

    public function getYearly() {
        $sql = "
            SELECT YEAR(createAt) AS `year`,
                   SUM(finalTotal)  AS `total`
              FROM payment
          GROUP BY `year`
          ORDER BY `year`
        ";
        return $this->db->prepareSelect($sql);
    }

}