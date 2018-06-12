<?php
/**
 * Created by PhpStorm.
 * User: changlinli 
 * Date: 14-10-21
 * Time: 上午11:26
 */

namespace Inauth\Package\Sync;


class Sync
{
    static $queueconf = array(
        'gz'=>array(
            "memcache",
            "hmemcache",
        ),
        'syq'=>array(
            "memcache",
            "hmemcache",
        ),
        'dfz'=>array(
            "memcache",
            "hmemcache",
        ),
    );
//    static $serverconf = array(
//
//    'gz'=>array(
//                array('ip'=>'10.0.4.44',// gz1广州1
//                 'port'=>8181),
//                ),
//    'syq'=>array(
//                array('ip'=>'172.16.8.181',// syq1三元桥1
//                 'port'=>8181),
////                array('ip'=>'172.16.12.179',// syq1三元桥1
////                'port'=>8181),
//                ),
//    'dfz'=>array(
//                array('ip'=>'10.5.0.71',// dfz1定福庄1
//                 'port'=>8181),
//                ),
//    'local'=>array(
//                array('ip'=>'172.0.0.1',// dfz1定福庄1
//                'port'=>8181),
//
//                ),
//    );

    static $serverconf = array(

        'gz'=>array(
            array('ip'=>'10.0.26.44',// gz1广州1
                'port'=>8182),
        ),
        'syq'=>array(
            array('ip'=>'10.8.8.41',// syq1三元桥1
                'port'=>8182),
        ),
        'dfz'=>array(
            array('ip'=>'10.5.12.16',// dfz1定福庄1
                'port'=>8182),
        ),

    );

    /**
     * 获得本地ip
     */
    public  static function ip()
    {
        return gethostbyname(gethostname());
        $ip = $_SERVER['SERVER_ADDR'];
        if(empty($ip))
        {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        }
        if(empty($ip))
        {
            $ip = "127.0.0.1";
        }
        return $ip;
    }

     /**
      * 同步数据
      */
    public static function sync($queue, $dt)
    {
        if(strlen($queue) > 16)
        {
            $error['result'] = "queue name is too long";
            $error['queue'] = $queue;
            
            \Inauth\Package\Util\Utilities::Log('computer_room_sync_'.$queue, print_r($error, TRUE));
            
            return;
        }
        $room = self::computeroom();
     //   if(in_array(self::$queueconf[$room], $queue))
        {

        }

        $input['from'] = $room;


        $input['queue'] = $queue;
        //$input['dt'] = serialize(func_get_args());
        $input['data'] = json_encode($dt);


        $servers = self::$serverconf[$room];

       // $result = sync($input, $servers[0]);调用扩展
        $result = self::send($input, $servers[rand(0,count($servers)-1)]);
        if(is_string($result))
        {
            $error['result'] = $result;
            $error['queue'] = $queue;
            $log_str = "result:{$error['result']}\tqueue:{$queue}";
            \Inauth\Package\Util\Utilities::Log('computer_room_sync_'.$queue, $log_str);
        }
        else
        {
            $info['dt'] = $dt;
            $info['from'] = $room;
            $info['ip'] = self::ip();
            $info['queue'] = $queue;
            $info['server'] = $servers[0];
            $log_str = '';
            if(!empty($dt)){
                foreach($dt as $k=>$v){
                    $log_str .= "{$k}:{$v}\t";
                }
            }

            $log_str .= "from:{$info['from']}\tip:{$info['ip']}\tqueue:{$info['queue']}\tserver:{$info['server']['ip']}\t";
            \Inauth\Package\Util\Utilities::Log('sync_record_'.$queue, $log_str);
            
        }
        return;
    }
     /**
      * 发送数据
      */
    private static function send(&$input, $server)
    {
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock === false)
        {
            return  "socket_create() failed:reason:" . socket_strerror( socket_last_error() );
        }
        $datalen = strlen($input['data']);
        $header = pack("a8a16Na32", $input['from'], $input['queue'], $datalen, "0");
        $data = $header.$input['data'];

        $len = $datalen + 60;

        $sent = socket_sendto($sock, $data, $len, 0, $server['ip'], $server['port']);
        if(!$sent)
        {
            socket_close($sock);
            return  "socket_sendto() failed:reason:" . socket_strerror( socket_last_error() );
        }
        socket_close($sock);
        return true;
    }

    /**
     * 判断机房
     */
    public static function computeroom()
    {
        $ip = self::ip();
        $ip = trim($ip);
        $nodes = explode(".", $ip);
        if($nodes[0] == '183' && $nodes[1] == '60')
        {
            return 'gz';//广州机房
        }
        elseif($nodes[0] == '10' && $nodes[1] == '0')
        {
            return 'gz';//广州机房
        }
        elseif($nodes[0] == '163' && $nodes[1] == '177')
        {
            return 'gz';//广州机房
        }
        elseif($nodes[0] == '10' && $nodes[1] == '5')
        {
            return 'dfz';//定福庄机房
        }
        elseif($nodes[0] == '118' && $nodes[1] == '194')
        {
            return 'dfz';//定福庄机房
        }
        elseif($nodes[0] == '172' && $nodes[1] == '16')
        {
            return 'syq';//三元桥机房
        }
        elseif($nodes[0] == '124' && $nodes[1] == '202')
        {
            return 'syq';//三元桥机房
        }
        elseif($nodes[0] == '10' && $nodes[1] == '8')
        {
            return 'syq';//三元桥机房
        }
        else
        {
            return 'local';
        }

    }

    public static function consume($queue, $callback)
    {
        $conn = new \AMQPConnection;
        $conn->connect();
        $channel = new \AMQPChannel($conn);

        $q = new \AMQPQueue($channel);
        $q->setName($queue['name']);

        if($queue['durable'])
        {
            $q->setFlags(AMQP_DURABLE);
            $q->declare();
        }

        while(true)
        {
            $messages = $q->get(AMQP_AUTOACK);
            if($messages)
            {
                call_user_func($callback, $messages->getBody());
            }
            else
            {
                usleep(2000);
            }

        }
        $conn->disconnect();
    }
}
