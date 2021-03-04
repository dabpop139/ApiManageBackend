package models

import (
	"../utils"
	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
)

var MyOrm *gorm.DB
var TbPre string

func init() {
	cfgParam, err := utils.LoadConfigFile("env.ini", ".")
	if err != nil {
		panic("读取配置文件错误!")
	}
	TbPre = cfgParam.Get("database.prefix").(string)

	var errSql error
	MyOrm, errSql = gorm.Open("mysql", cfgParam.Get("database.dsn"))
	//defer Db.Close()
	MyOrm.LogMode(true)
	if errSql != nil {
		panic("数据库连接失败!")
	}
	MyOrm.Callback().Create().Replace("gorm:update_time_stamp", updateTimeStampForCreateCallback)
	MyOrm.Callback().Update().Replace("gorm:update_time_stamp", updateTimeStampForUpdateCallback)
	MyOrm.AutoMigrate(&ApiResource{})
}
