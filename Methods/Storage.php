<?php
require_once 'HIDDEN';

use HIDDEN;

class Storage
{

    public function __construct($db_table_name)
    {
        $this->db_table_name = $db_table_name;

        define('DB_HOST', '');
        define('DB_USER', '');
        define('DB_PASSWORD', '');
        define('DB_NAME', '');

        HIDDEN::initializeConnection();
        $this->db = HIDDEN::getInstance();
    }

    function addCompany($csrf, $sessionid, $email, $password)
    {
        $company = [
            'csrf' => $csrf,
            'sessionid' => $sessionid,
            'email' => $email,
            'password' => $password,
            'status' => 'initialized.',
            'done' => 0,
            'clearwarehouse' => 0,
            'buysupply' => 0,
            'morestores' => 0,
            'sellcoffee' => 0,
            'contracts' => 0,
        ];
        $this->db->insertRow($this->db_table_name, $company);
    }
    function deleteCompany($email)
    {
        $company = [
            'email' => $email,
        ];
        $this->db->deleteRow($this->db_table_name, $company);
    }
    function getCompanies()
    {
        $results = $this->db->executeQuery("SELECT * FROM {$this->db_table_name}");

        $res = [];
        while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
            array_push($res, $row);
        }
        $results->free();

        return $res;
    }
    function editCompany($email, $values)
    {
        $company = [
            'email' => $email,
        ];
        $this->db->updateRow($this->db_table_name, $company, $values);
    }
}
