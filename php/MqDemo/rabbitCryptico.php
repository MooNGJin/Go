<?php
/**
 * Created by PhpStorm.
 * User: crazy
 * Date: 2019/2/12
 * Time: 17:04
 */

class Rabbit {
    private $x = [];
    private $c = [];
    private $carry;

    public function __construct() {
        for($i = 0; $i < 8; $i++){
            $this->x[$i] = $this->c[$i] = 0;
        }
        $this->carry = 0;
    }

    private static function uRight($a, $n) {
        $c = 2147483647 >> ($n - 1);

        return $c & ($a >> $n);
    }

    private function g_func($x) {
        $a = $x & 0xffff;
        $b = self::uRight($x, 16);

        $h = (self::uRight(((self::uRight($a * $a, 17)) + ($a * $b)), 15)) + $b * $b;
        $l = $x * $x;

        return $h ^ $l;
    }

    /**
     * @declaration 比较两个有符号整数的十六进制的大小，即作为无符号整数进行比较
     * @param x
     * @param y
     * @return 若(unsigned x) < (unsigned y)，则返回1，否则返回0
     */
    private function compare($x, $y) {
        $a = $x;
        $b = $y;
        $a &= 0x00000000ffffffff;
        $b &= 0x00000000ffffffff;

        return ($a < $b) ? 1 : 0;
    }

    private function rotL($x, $y) {
        return ($x << $y) | (self::uRight($x, (32 - $y)));
    }

    private function next_state() {
        $g = [];
        $c_old = [];
        $i = 0;

        for( $i = 0; $i < 8; $i++) {
            $c_old[$i] = $this->c[$i];
        }

        $this->c[0] += 0x4d34d34d + $this->carry;
        $this->c[1] += 0xd34d34d3 + $this->compare($this->c[0], $c_old[0]);
        $this->c[2] += 0x34d34d34 + $this->compare($this->c[1], $c_old[1]);
        $this->c[3] += 0x4d34d34d + $this->compare($this->c[2], $c_old[2]);
        $this->c[4] += 0xd34d34d3 + $this->compare($this->c[3], $c_old[3]);
        $this->c[5] += 0x34d34d34 + $this->compare($this->c[4], $c_old[4]);
        $this->c[6] += 0x4d34d34d + $this->compare($this->c[5], $c_old[5]);
        $this->c[7] += 0xd34d34d3 + $this->compare($this->c[6], $c_old[6]);
        $this->carry = $this->compare($this->c[7], $c_old[7]);

        for( $i = 0; $i < 8; $i++)
        {
            $g[$i] = $this->g_func($this->x[$i] + $this->c[$i]);
        }

        $this->x[0] = $g[0] + $this->rotL($g[7], 16) + $this->rotL($g[6], 16);
        $this->x[1] = $g[1] + $this->rotL($g[0], 8 ) + $g[7];
        $this->x[2] = $g[2] + $this->rotL($g[1], 16) + $this->rotL($g[0], 16);
        $this->x[3] = $g[3] + $this->rotL($g[2], 8 ) + $g[1];
        $this->x[4] = $g[4] + $this->rotL($g[3], 16) + $this->rotL($g[2], 16);
        $this->x[5] = $g[5] + $this->rotL($g[4], 8 ) + $g[3];
        $this->x[6] = $g[6] + $this->rotL($g[5], 16) + $this->rotL($g[4], 16);
        $this->x[7] = $g[7] + $this->rotL($g[6], 8 ) + $g[5];
    }

    /**
     * @declaration 将一个字节数组转化为整数，采用Big-Endian格式进行解析
     * @param a 待转化的字节数组
     * @param i 字节数组的起始索引
     * @return 转化后的整数
     */
    public static function os2ip($a, $i) {
        $x0 = $a[$i + 3] & 0x000000ff;
        $x1 = $a[$i + 2] << 8 & 0x0000ff00;
        $x2 = $a[$i + 1] << 16 & 0x00ff0000;
        $x3 = $a[$i] << 24 & 0xff000000;

        return $x0 | $x1 | $x2 | $x3;
    }

    public static function integerToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        $byt[2] = ($val >> 16 & 0xff);
        $byt[3] = ($val >> 24 & 0xff);
        return $byt;
    }

    /**
     * @declaration 将整数x转化为4字节数组，采用Big-Endian格式进行解析
     * @param x 待转化的整数x
     * @return 解析后的字节数组，长度为4
     */
    public static function i2osp($x) {
        $s = [];
        $s[3] = ($x & 0x000000ff);
        $s[2] = self::uRight(($x & 0x0000ff00), 8);
        $s[1] = self::uRight(($x & 0x00ff0000), 16);
        $s[0] = self::uRight(($x & 0xff000000), 24);

        return $s;
    }

    /**
     * @declaration 密钥初始化函数
     * @param p_key 长度为128位的密钥数组，若密钥长度小于128位，
     *               则必须在填充后再调用该函数
     */
    public function keySetup($p_key) {
        $k0 = $k1 = $k2 = $k3 = $i = 0;
        $k0 = self::os2ip($p_key, 12);
        $k1 = self::os2ip($p_key, 8);
        $k2 = self::os2ip($p_key, 4);
        $k3 = self::os2ip($p_key, 0);
        $x[0] = $k0;
        $x[2] = $k1;
        $x[4] = $k2;
        $x[6] = $k3;
        $x[1] = ($k3 << 16) | (self::uRight($k2, 16));
        $x[3] = ($k0 << 16) | (self::uRight($k3, 16));
        $x[5] = ($k1 << 16) | (self::uRight($k0, 16));
        $x[7] = ($k2 << 16) | (self::uRight($k1, 16));
        $c[0] = $this->rotL($k2, 16);
        $c[2] = $this->rotL($k3, 16);
        $c[4] = $this->rotL($k0, 16);
        $c[6] = $this->rotL($k1, 16);
        $c[1] = ($k0 & 0xffff0000) | ($k1 & 0x0000ffff);
        $c[3] = ($k1 & 0xffff0000) | ($k2 & 0x0000ffff);
        $c[5] = ($k2 & 0xffff0000) | ($k3 & 0x0000ffff);
        $c[7] = ($k3 & 0xffff0000) | ($k0 & 0x0000ffff);
        $this->carry = 0;
        for ($i = 0; $i < 4; $i++) {
            $this->next_state();
        }
        for ($i = 0; $i < 8; $i++) {
            $c[($i + 4) & 7] ^= $x[$i];
        }
    }

    /**
     * @declaration 该函数用于生成128位随机密钥，用于直接和明文进行异或处理
     * @param p_dest  产生的128位随机密钥
     * @param data_size 需要产生的随机密钥数量，必须是16的倍数
     */
    public function getS($p_dest, $data_size) {
        $i = $j = $m = 0;
        $k = [];
        $t = [];
        for ($i = 0; $i < $data_size; $i += 16) {
            $this->next_state();
            $k[0] = $this->x[0] ^ (self::uRight($this->x[5] , 16)) ^ ($this->x[3] << 16);
            $k[1] = $this->x[2] ^ (self::uRight($this->x[7] , 16)) ^ ($this->x[5] << 16);
            $k[2] = $this->x[4] ^ (self::uRight($this->x[1] , 16)) ^ ($this->x[7] << 16);
            $k[3] = $this->x[6] ^ (self::uRight($this->x[3] , 16)) ^ ($this->x[1] << 16);
            for ($j = 0; $j < 4; $j++) {
                $t = self::i2osp($k[$j]);
                for ($m = 0; $m < 4; $m++) {
                    $p_dest[$j * 4 + $m] = $t[$m];
                }
            }
        }
    }

    /**
     * @declaration 加密和解密函数
     * @param p_src 需要加密或解密的消息，其长度必须是16的倍数，以字节为单位，
     *               若不是16的倍数，则需要在调用该函数前进行填充，一般填充值
     *               为0的字节
     * @param p_dest 加密或解密的结果，其长度必须是16的倍数，以字节为单位
     *                并且长度必须大于等于p_src的长度
     * @param data_size 需要处理的消息的长度，必须是16的倍数，以字节为单位
     *                   其值为p_src的长度
     */
    public function cipher($p_src, $p_dest, $data_size) {
        $i = $j = $m = 0;
        $k = [];
        $t = [];
        for ($i = 0; $i < $data_size; $i += 16) {
            $this->next_state();
            $k[0] = self::os2ip($p_src, $i * 16 + 0) ^ $this->x[0] ^ (self::uRight($this->x[5], 16)) ^ ($this->x[3] << 16);
            $k[1] = self::os2ip($p_src, $i * 16 + 4) ^ $this->x[2] ^ (self::uRight($this->x[7], 16)) ^ ($this->x[5] << 16);
            $k[2] = self::os2ip($p_src, $i * 16 + 8) ^ $this->x[4] ^ (self::uRight($this->x[1], 16)) ^ ($this->x[7] << 16);
            $k[3] = self::os2ip($p_src, $i * 16 + 12) ^ $this->x[6] ^ (self::uRight($this->x[3], 16)) ^ ($this->x[1] << 16);
            for ($j = 0; $j < 4; $j++) {
                $t = self::i2osp($k[$j]);
                for ($m = 0; $m < 4; $m++) {
                    $p_dest[$i * 16 + $j * 4 + $m] = $t[$m];
                }
            }
        }
        return $p_dest;
    }

}

/**
 * 定义测试密钥key，需要注意的是，由于java没有unsigned类型，
 * 所以需要对大于等于0x80的字节进行类型转换，
 * 比较方便的办法是都加上(byte)
 */
$key = [
    0xa0, 0x33, 0xd6, 0x78,
    0x6b, 0x05, 0x14, 0xac,
    0xfc, 0x3d, 0x8e, 0x2d,
    0x6a, 0x2c, 0x27, 0x1d,
];
/**
 * 定义待加密的消息message，密文ciphertext，并初始化为0
 */
$message = [];
$ciphertext = [];
for( $i = 0; $i < 16; $i++){
    $message[$i] = $i;
    $ciphertext[$i] = 0;
}
/**
 * 调用Rabbit流密码进行加密
 */
$rtest = new Rabbit();
/*
 * 初始化密钥
 */
$rtest->keySetup($key);
/*
 * 加密
 */
$a = $rtest->cipher($message, $ciphertext, 16);
print_r($a);

//for ($i = 0; $i < 16; $i++) {
//    printf("%02x ", $ciphertext[$i]);
//}
print_r('____s____');
/**
 * 调用Rabbit流密码进行解密
 */
$rtest2 = new Rabbit();
/*
 * 初始化密钥
 */
$rtest2->keySetup($key);
$szT = [];
for ($i = 0; $i < 16; $i++) {
    $szT[$i] = 0;
}
/*
 * 解密
 */
$b = $rtest2->cipher($ciphertext, $szT, 16);
print_r($b);











