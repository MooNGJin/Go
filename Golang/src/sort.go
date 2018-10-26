package main

import (
	"math/rand"
	"time"
	"sortMethod"
	"fmt"
)

const (
	num      = 10
	rangeNum = 100000
)

func main()  {
	randSeed := rand.New(rand.NewSource(time.Now().Unix() + time.Now().UnixNano()))
	var buf []int
	for i := 0; i < num; i++ {
		buf = append(buf, randSeed.Intn(rangeNum))
	}
	t := time.Now()

	//// 冒泡
	//sortMethod.Maopao(buf) // maopao times:  2497325925
	//fmt.Println(time.Since(t)) // 18.4659323s

	//// 选择
	//sortMethod.Xuanze(buf) // xuanze times:  5000049999
	//fmt.Println(time.Since(t)) // 6.6153471s

	//// 插入
	//sortMethod.Charu(buf) // charu times:  2494458379
	//fmt.Println(time.Since(t)) // 4.7688295s

	//// 希尔
	//sortMethod.Shell(buf) // xier times:  4313802
	//fmt.Println(time.Since(t)) // 14.5066ms

	//// 快速
	//sortMethod.Kuaisu(buf) // kuaisu times:  64495
	//fmt.Println(time.Since(t)) // 7.0058ms

	//// 归并
	sortMethod.Guibing(buf)
	fmt.Println(time.Since(t)) // 10.5069ms

	//// 堆
	//sortMethod.Duipai(buf)
	//fmt.Println(time.Since(t)) // 11.008ms

}


