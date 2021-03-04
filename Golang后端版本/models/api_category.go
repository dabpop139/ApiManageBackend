package models

type ApiProjectTree struct {
	Project   *ApiCategory `json:"project"`
	Subcates  []*ApiCategory `json:"subcates"`
}

type ApiCategory struct {
	BaseModel `mapstructure:",squash"`                               //squash是map转结构体时能解析继承的类用的(不能省略前面的逗号)
	Pid       int            `json:"pid"  gorm:"not null;defult:0"`  //图片宽度
	Name      string         `json:"name" gorm:"not null;defult:''"` //图片宽度
	Ord       int            `json:"ord"  gorm:"not null;defult:0"`
	Dlists    []*ApiResource `json:"dlists"`
}

func (ApiProjectTree) TableName() string {
	return TbPre + "api_category"
}

func (ApiCategory) TableName() string {
	return TbPre + "api_category"
}