<?php
namespace Inauth\Config\Passport;
require_once '/home/work/conf/api/MySQLConfigApi.php';

class MySQL extends \Inauth\Libs\Singleton  {

    const MYSQL_KEY = 'virus';

    public function configs() {
        return \MySQLConfigApi::GetCfgByServKey(self::MYSQL_KEY);
    }
}
