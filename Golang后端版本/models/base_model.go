package models

import (
	"github.com/jinzhu/gorm"
	"time"
)

type SendResult struct {
	Status int    `json:"status"`
	Extime string `json:"extime"`
	Header string `json:"header"`
	Raw    string `json:"raw"`
}

//基础model, 别的表可以继承此表
type BaseModel struct {
	Id         int64 `json:"id"         gorm:"primary_key"`
	Createtime int64 `json:"createtime" gorm:"not null"` //时间戳, unix, 秒 10位
	Updatetime int64 `json:"updatetime" gorm:"not null"`
}

// updateTimeStampForCreateCallback will set `CreatedOn`, `ModifiedOn` when creating
func updateTimeStampForCreateCallback(scope *gorm.Scope) {
	if !scope.HasError() {
		if createTimeField, ok := scope.FieldByName("Createtime"); ok {
			if createTimeField.IsBlank {
				createTimeField.Set(time.Now().Unix())
			}
		}

		if modifyTimeField, ok := scope.FieldByName("Updatetime"); ok {
			if modifyTimeField.IsBlank {
				modifyTimeField.Set(time.Now().Unix())
			}
		}
	}
}

// updateTimeStampForUpdateCallback will set `Updatetime` when updating
func updateTimeStampForUpdateCallback(scope *gorm.Scope) {
	// 为什么是gorm:update_column
	if _, ok := scope.Get("gorm:update_column"); !ok {
		scope.SetColumn("Updatetime", time.Now().Unix())
	}
}
