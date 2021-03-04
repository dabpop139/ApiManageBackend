package utils

import (
	"fmt"
	"github.com/json-iterator/go"
	"net/http"
	"time"
)

// Response 向客户端返回数据的
type Response struct {
	r *http.Request
	w http.ResponseWriter
}

// NewResponse 创建一个新的response对象
func NewResponse(w http.ResponseWriter, r *http.Request) Response {
	r.ParseForm()
	return Response{
		w: w,
		r: r,
	}
}

// ReturnSuccess 返回正确的信息
func (r *Response) DealOptionReq() {
	r.Return(http.StatusOK, 1, "", nil)
}

// ReturnSuccess 返回正确的信息
func (r *Response) ReturnSuccess(data interface{}, msg string) {
	r.Return(http.StatusOK, 1, msg, data)
}

// ReturnError 返回错误信息
func (r *Response) ReturnError(statuscode, code int, errMsg string) {
	r.Return(statuscode, code, errMsg, nil)
}

// Return 向客户返回回数据
func (r *Response) Return(statusCode int, code int, msg string, data interface{}) {
	jsonp := r.IsJSONP()

	datatpl := map[string]interface{}{"code": code, "msg": msg, "time": time.Now().Unix(), "data": data}
	var jsoner = jsoniter.ConfigCompatibleWithStandardLibrary
	rs, err := jsoner.Marshal(datatpl)
	if err != nil {
		code = 500
		rs = []byte(fmt.Sprintf(`{"code":0, "msg":"%s", "time":%d, "data":null}`, err.Error(), time.Now().Unix()))
	}

	// 当我们使用Set时候，如果原来这一项已存在，后面的就修改已有的
	// 当使用Add时候，如果原本不存在，则添加，如果已存在，就不做任何修改
	r.w.Header().Add("Access-Control-Allow-Origin", "*")
	r.w.Header().Add("Access-Control-Allow-Headers", "DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,X-User-Token,If-Modified-Since,Cache-Control,Content-Type,Accept-Language,Origin,Accept-Encoding")

	if jsonp == "" {
		r.w.Header().Add("Content-Type", "application/json")
		r.w.WriteHeader(statusCode)
		r.w.Write(rs)
	} else {
		r.w.Header().Add("Content-Type", "application/javascript")
		r.w.WriteHeader(statusCode)
		r.w.Write([]byte(fmt.Sprintf(`%s(%s)`, jsonp, rs)))
	}
}

// IsJSONP 是否为jsonp 请求
func (r *Response) IsJSONP() string {
	if r.r.Form.Get("callback") != "" {
		return r.r.Form.Get("callback")
	}
	return ""
}
