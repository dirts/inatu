<?php
namespace Inauth\Scripts;

/**
 * 用于清理 90 天前存于 DB 的 session 数据
 */

use \Inauth\Package\Session\Helper\DBOctopusHelper;

class DeleteExpireSessions extends \Frame\Script {

    public function run() {

        $today = date('Y-m-d');
        $three_month_ago = date('Y-m-d', time() - 90 * 86400);

        // 使用 $current_id 作查询边界，防止主从不同步时重复读取
        // TODO: 考虑直接从主库查
        $current_id = 0;

        while (TRUE) {
            $sql = "SELECT * FROM `t_octopus_session_info` WHERE `id`>{$current_id} AND `login`<'{$three_month_ago}' ORDER BY `id` ASC LIMIT 100";
            $result = DBOctopusHelper::getConn()->read($sql, array());
            if (empty($result)) {
                break;
            }

            $id_set = array();
            foreach ($result as $item) {
                $id = $item['id'];
                if ($current_id < $id) $current_id = $id;
                $id_set[] = $id;
            }

            $id_str = implode(',', $id_set);

            $sqlDelete = "DELETE FROM `t_octopus_session_info` WHERE `id` IN ({$id_str})";
            var_dump($sqlDelete);
            //TODO: uncomment write, add logs;
            //$result = DBOctopusHelper::getConn()->write($sqlDelete, array());
        }

        $this->response->setBody("script demo");
    }
}
