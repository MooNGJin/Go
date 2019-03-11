<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/6
 * Time: 下午 14:45
 */

class ConsistentHash {
    // server列表
    private $_server_list = array();
    // 延迟排序，因为可能会执行多次addServer
    private $_layze_sorted = FALSE;

    public function printServiceList(){
        print_r($this->_server_list);
    }

    private function myHash($str) {
        // hash(i) = hash(i-1) * 33 + str[i]
        $hash = 0;
        $s    = md5($str);
        $seed = 5;
        $len  = 32;
        for ($i = 0; $i < $len; $i++) {
            // (hash << 5) + hash 相当于 hash * 33
            //$hash = sprintf("%u", $hash * 33) + ord($s{$i});
            //$hash = ($hash * 33 + ord($s{$i})) & 0x7FFFFFFF;
            $hash = ($hash << $seed) + $hash + ord($s{$i});
        }

        return $hash & 0x7FFFFFFF;
    }

    public function addServer($server) {
        $hash = $this->myHash($server);
        $this->_layze_sorted = FALSE;

        if (!isset($this->_server_list[$hash])) {
            $this->_server_list[$hash] = $server;
        }

        return $this;
    }

    public function find($key) {
        // 排序
        if (!$this->_layze_sorted) {
            asort($this->_server_list);
            $this->_layze_sorted = TRUE;
        }

        $hash = myHash($key);
        $len  = sizeof($this->_server_list);
        if ($len == 0) {
            return FALSE;
        }

        $keys   = array_keys($this->_server_list);
        $values = array_values($this->_server_list);

        // 如果不在区间内，则返回最后一个server
        if ($hash <= $keys[0] || $hash >= $keys[$len - 1]) {
            return $values[$len - 1];
        }

        foreach ($keys as $key=>$pos) {
            $next_pos = NULL;
            if (isset($keys[$key + 1]))
            {
                $next_pos = $keys[$key + 1];
            }

            if (is_null($next_pos)) {
                return $values[$key];
            }

            // 区间判断
            if ($hash >= $pos && $hash <= $next_pos) {
                return $values[$key];
            }
        }
    }
}

$consisHash = new ConsistentHash();
$consisHash->addServer("serv1")->addServer("serv2")->addServer("serv3");
$consisHash->printServiceList();
echo "key1 at " . $consisHash->find("kadsasd1") . ".\n";
echo "key2 at " . $consisHash->find("465s31d") . ".\n";
echo "key3 at " . $consisHash->find("ked3") . ".\n";
