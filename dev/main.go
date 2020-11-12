package main

import (
	"fmt"
	"github.com/gorhill/cronexpr"
	"time"
)
func main() {
nextTime := cronexpr.MustParse("*/5 9-17 * * 1-5").NextN(time.Now(),10)
for _,v:=range nextTime {
	fmt.Printf("%v\n",v)
	
}
fmt.Println(nextTime[2])
}