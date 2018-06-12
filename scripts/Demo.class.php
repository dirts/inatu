<?php

namespace Inauth\Scripts;
use \Inauth\Package\Session\Session;

class Demo extends \Frame\Script {

    public function run() {

        $ticket = session::get_ticket(121324);
        var_dump($ticket);

        $data = session::get_user_tickets(12312, 243423);
        var_dump($ticket);
        exit;
    }
}
