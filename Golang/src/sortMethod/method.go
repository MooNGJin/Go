package sortMethod

import "fmt"

// 冒泡排序
func Maopao(buf []int) {
	times := 0
	for i := 0; i < len(buf)-1; i++ { // 需要每一个元素都参与 二次比较
		flag := false
		for j := 1; j < len(buf)-i; j++ { // 内循环与外循环元素比较比较、前后，交换位置
			if buf[j-1] > buf[j] {
				times++
				tmp := buf[j-1]
				buf[j-1] = buf[j]
				buf[j] = tmp
				flag = true
			}
		}
		if !flag {
			break
		}
	}
	fmt.Println("maopao times: ", times)
}

// 选择排序
func Xuanze(buf []int) {
	times := 0
	for i := 0; i < len(buf)-1; i++ { // 假设第一个是最小的，然后一直比较下去，然后把小的放在上面
		min := i
		for j := i; j < len(buf); j++ {
			times++
			if buf[min] > buf[j] {
				min = j
			}
		}
		if min != i {
			tmp := buf[i]
			buf[i] = buf[min]
			buf[min] = tmp
		}
	}
	fmt.Println("xuanze times: ", times)
}

// 插入排序
func Charu(buf []int) {
	times := 0
	for i := 1; i < len(buf); i++ {
		for j := i; j > 0; j-- {
			if buf[j] < buf[j-1] { // 当第一个小于第二个元素的时候，进行交换, 当前元素与上一个元素进行比较
				times++
				tmp := buf[j-1]
				buf[j-1] = buf[j]
				buf[j] = tmp
			} else {
				break
			}
		}
	}
	fmt.Println("charu times: ", times)
}

// 希尔排序
func Shell(buf []int) {
	times := 0
	tmp := 0
	length := len(buf)
	incre := length

	for {
		incre /= 2 // 长度取半, 定为增量
		for k := 0; k < incre; k++ { // 根据增量分为若干子序列
			for i := k + incre; i < length; i += incre {
				for j := i; j > k; j -= incre {
					times++
					if buf[j] < buf[j-incre] { // 交换位置
						tmp = buf[j-incre]
						buf[j-incre] = buf[j]
						buf[j] = tmp
					} else {
						break
					}
				}
			}
		}
		if incre == 1 {
			break
		}
	}
	fmt.Println("xier times: ", times)
}

var num int = 0

// 快速排序
func Kuaisu(buf []int) {
	kuai(buf, 0, len(buf)-1)
	fmt.Println("kuaisu times: ", num)
}

func kuai(a []int, l, r int) {
	if l >= r {
		return
	}
	num += 1
	i, j, key := l, r, a[l] /*用数组的第一个记录作为分区元素*/
	for i < j {
		for i < j && a[j] > key { //从右向左找第一个小于key的值，J 向 I 靠近，且值 慢慢往左边最小的Key 靠近
			j-- /*从右向左扫描，找第一个码值小于key的记录，并交换到key*/
		}
		if i < j { // 移动完了，发现 J 移动的次数I少，证明 第I次是较小的
			a[i] = a[j] // 把最小值记录给I
			i++ // 移动一个位置
		}

		for i < j && a[i] < key { //从左向右找第一个大于key的值， I 向 J 靠近，且值 慢慢往右边最大的Key 靠近
			i++  /*从左向右扫描，找第一个码值大于key的记录，并交换到右边*/
		}
		if i < j { // 移动完了，发现 I移动的次数J少，证明 第I次是较大的
			a[j] = a[i]
			j--
		}
	}
	/*分区元素放到正确位置*/
	a[i] = key // 记录 当前 值给I
	kuai(a, l, i-1)
	kuai(a, i+1, r)
}

//归并排序
func Guibing(buf []int) {
	tmp := make([]int, len(buf))
	merge_sort(buf, 0, len(buf)-1, tmp)
	fmt.Println(buf)
}

func merge_sort(a []int, first, last int, tmp []int) {
	if first < last {
		middle := (first + last) / 2
		merge_sort(a, first, middle, tmp)       //左半部分排好序
		merge_sort(a, middle+1, last, tmp)      //右半部分排好序
		mergeArray(a, first, middle, last, tmp) //合并左右部分
	}
}

func mergeArray(a []int, first, middle, end int, tmp []int) {
	fmt.Printf("mergeArray a: %v, first: %v, middle: %v, end: %v, tmp: %v\n",
	    a, first, middle, end, tmp)
	i, m, j, n, k := first, middle, middle+1, end, 0
	for i <= m && j <= n {
		if a[i] <= a[j] {
			tmp[k] = a[i]
			k++
			i++
		} else {
			tmp[k] = a[j]
			k++
			j++
		}
	}
	for i <= m {
		tmp[k] = a[i]
		k++
		i++
	}
	for j <= n {
		tmp[k] = a[j]
		k++
		j++
	}

	for ii := 0; ii < k; ii++ {
		a[first+ii] = tmp[ii]
	}
	// fmt.Printf("sort: buf: %v\n", a)
}

// 堆排序
func Duipai(buf []int) {
	temp, n := 0, len(buf)

	for i := (n - 1) / 2; i >= 0; i-- {
		MinHeapFixdown(buf, i, n)
	}

	for i := n - 1; i > 0; i-- {
		temp = buf[0]
		buf[0] = buf[i]
		buf[i] = temp
		MinHeapFixdown(buf, 0, i)
	}
}

func MinHeapFixdown(a []int, i, n int) {
	j, temp := 2*i+1, 0
	for j < n {
		if j+1 < n && a[j+1] < a[j] {
			j++
		}

		if a[i] <= a[j] {
			break
		}

		temp = a[i]
		a[i] = a[j]
		a[j] = temp

		i = j
		j = 2*i + 1
	}
}