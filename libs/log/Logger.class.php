<?php
namespace Inauth\Libs\Log;
class Logger extends \Libs\Log\ProxyLogWriter
{
    public function __construct() {
        $collector = new \Libs\Log\ScribeLogCollector();
        parent::__construct($collector);
    }
}
