package models

type ApiResource struct {
	BaseModel   `mapstructure:",squash"`                             //squash是map转结构体时能解析继承的类用的(不能省略前面的逗号)
	Projectid   int                 `json:"projectid"                gorm:"not null;defult:0"` //图片宽度
	Cateid      int                 `json:"cateid"                   gorm:"not null;defult:0"` //图片宽度
	Apiname     string              `json:"apiname"                  gorm:"not null;defult:''"`
	Apiuri      string              `json:"apiuri"                   gorm:"not null;defult:''"`
	Reqmethod   string              `json:"reqmethod"                gorm:"not null;defult:''"`
	Rawdata     string              `json:"rawdata_origin,omitempty" gorm:"not null;defult:''"`
	Respraw     string              `json:"respraw"                  gorm:"not null;defult:''"`
	ApiResourceRawdata `mapstructure:",squash"`
}

type ApiResourceRawdata struct {
	Reqscheme   string `json:"reqscheme"`
	Bodytype    string `json:"bodytype"`
	Bodyrawtype string `json:"bodyrawtype"`
	Rheader_chk bool   `json:"rheader_chk"`
	Rbody_chk   bool   `json:"rbody_chk"`
	Rheader     string `json:"rheader"`
	Rbody       string `json:"rbody"`
}

func (ApiResource) TableName() string {
	return TbPre+"api_resource"
}