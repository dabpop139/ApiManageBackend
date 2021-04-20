package main

import (
	"./models"
	"./utils"
	"flag"
	"fmt"
	"github.com/go-resty/resty"
	"github.com/goinggo/mapstructure"
	"github.com/json-iterator/go"
	"log"
	"net/http"
	"os"
	"path"
	"runtime"
	"strconv"
	"strings"
)

var staticHandler http.Handler
// 初始化参数
func init() {
	dir := path.Dir(os.Args[0])
	staticHandler = http.FileServer(http.Dir(dir))
}

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU())
	port := flag.String("port", "8045", "HTTP 服务端口号")
	flag.Parse()

	// 下面开始加载 http 相关的服务
	http.HandleFunc("/", StaticServer)
	http.HandleFunc("/app.php/api/apictrl/handle", apictrlHandle)
	http.HandleFunc("/app.php/api/apictrl/apidata", apictrlApidata)
	http.HandleFunc("/app.php/api/apictrl/category", apictrlCategory)
	http.HandleFunc("/app.php/api/apictrl/operate", apictrlOperate)

	log.Printf("开始监听网络端口:%s", *port)

	if err := http.ListenAndServe(fmt.Sprintf(":%s", *port), nil); err != nil {
		log.Println(err)
	}
}

// 静态文件处理
func StaticServer(w http.ResponseWriter, r *http.Request) {
	//fmt.Println(r.URL.Path)
	if r.URL.Path != "/" {
		staticHandler.ServeHTTP(w, r)
		return
	}
	http.ServeFile(w,r,"index.html")
	//io.WriteString(w, f.Read)
}

/**
 * 接口保存
 */
func apictrlHandle(w http.ResponseWriter, r *http.Request) {
	fields := [...]string{"act", "projectid", "cateid", "aid", "apiname", "reqscheme", "apiuri"}
	mapFields := make(map[string]string)

	r.ParseForm()
	resp := utils.NewResponse(w, r)

	if r.Method == "OPTIONS" {
		resp.DealOptionReq()
		return
	}


	for _, v := range fields {
		mapFields[v] = r.Form.Get(v)
		if mapFields[v] == "" {
			//fmt.Println(v)
			resp.ReturnError(http.StatusOK, 0, "参数错误")
			return
		}
	}

	act := mapFields["act"]
	projectid, _ := strconv.Atoi(mapFields["projectid"])
	cateid, _ := strconv.Atoi(mapFields["cateid"])
	aid, _ := strconv.ParseInt(mapFields["aid"], 10, 64)
	apiname := strings.TrimSpace(mapFields["apiname"])
	apiuri := strings.TrimSpace(mapFields["apiuri"])

	reqscheme := mapFields["reqscheme"]
	reqmethod := r.Form.Get("reqmethod")

	bodytype := r.Form.Get("bodytype")
	bodyrawtype := r.Form.Get("bodyrawtype")

	rheader_chk := false
	if r.Form.Get("rheader_chk") == "true" {
		rheader_chk = true
	}
	rbody_chk := false
	if r.Form.Get("rbody_chk") == "true" {
		rbody_chk = true
	}

	rheader := strings.TrimSpace(r.Form.Get("rheader"))
	rbody := strings.TrimSpace(r.Form.Get("rbody"))

	// 处理保存
	if (act == "save") {
		rawdata := make(map[string]interface{})
		rawdata["reqscheme"] = reqscheme
		rawdata["bodytype"] = bodytype
		rawdata["bodyrawtype"] = bodyrawtype
		rawdata["rheader_chk"] = rheader_chk
		rawdata["rbody_chk"] = rbody_chk
		rawdata["rheader"] = rheader
		rawdata["rbody"] = rbody

		saveData := make(map[string]interface{})
		saveData["projectid"] = projectid
		saveData["cateid"] = cateid
		saveData["apiname"] = apiname
		saveData["apiuri"] = apiuri
		saveData["reqmethod"] = reqmethod
		var jsoner = jsoniter.ConfigCompatibleWithStandardLibrary
		rawdataStr, _ := jsoner.Marshal(rawdata)
		saveData["rawdata"] = string(rawdataStr)

		var apiRes = new(models.ApiResource)
		err := mapstructure.Decode(saveData, apiRes)
		if err != nil {
			resp.ReturnError(http.StatusOK, 0, "发生内部错误")
			return
		}
		//fmt.Println(apiRes.TableName())
		//return


		hasRec := new(models.ApiResource)
		hasRec.Apiname = apiname
		models.MyOrm.Where("cateid = ? AND apiname = ?", cateid, hasRec.Apiname).First(hasRec)

		if aid <= 0 {
			if hasRec.Id > 0 {
				resp.ReturnError(http.StatusOK, 0, "接口名称有重名")
				return
			}
			err = models.MyOrm.Create(apiRes).Error
			if err != nil {
				resp.ReturnError(http.StatusOK, 0, "发生内部错误")
				return
			}
			aid = apiRes.Id
		} else {
			if hasRec.Id > 0 && hasRec.Id != aid {
				resp.ReturnError(http.StatusOK, 0, "接口名称有重名")
				return
			}
			// 重复利用hasRec
			hasRec.Id = aid
			err := models.MyOrm.Where("id = ?", hasRec.Id).First(hasRec).Updates(saveData).Error
			if err != nil {
				resp.ReturnError(http.StatusOK, 0, "操作失败")
				return
			}
		}

		resultMap := make(map[string]interface{})
		resultMap["aid"] = aid
		resp.ReturnSuccess(resultMap, "操作成功")
		return

	}

	contentTypeEnum := make(map[string]string)
	// bodytype
	contentTypeEnum["x-www-form-urlencoded"] = "application/x-www-form-urlencoded"
	contentTypeEnum["form-data"]             = "multipart/form-data"
	// bodyrawtype
	contentTypeEnum["json"]       = "application/json"
	contentTypeEnum["xml"]        = "text/xml"
	contentTypeEnum["javascript"] = "application/javascript"
	contentTypeEnum["plain"]      = "text/plain"
	contentTypeEnum["html"]       = "text/html"
	//contentTypeEnum["text"] = "text/html"
	// 处理发送
	if (act == "send") {

		if reqscheme == "HTTP" {

			var reqHeaders = make(map[string]string)
			if reqmethod == "POST" {
				cotype, ok := contentTypeEnum[bodytype]
				if ok {
					reqHeaders["Content-type"] = cotype
				}
				if bodytype == "raw" {
					cotype, ok = contentTypeEnum[bodyrawtype]
					if ok {
						reqHeaders["Content-type"] = cotype
					}
				}
			}

			if rheader_chk == true {
				headerArr := strings.Split(rheader, "\n")
				for _, line := range headerArr {
					line = strings.TrimSpace(line)
					if line == "" {
						continue
					}
					if line[0:2] == "//" {
						continue
					}
					pos := strings.Index(line, ":")
					key := line[0:pos]
					val := line[pos+1:]
					reqHeaders[key] = val
				}
			}

			var reqBodyRaw string
			var postBody = make(map[string]string)
			if rbody_chk == true && reqmethod == "POST" {
				if bodytype == "raw" {
					reqBodyRaw = rbody
				} else {
					bodyArr := strings.Split(rbody, "\n")
					for _, line := range bodyArr {
						line = strings.TrimSpace(line)
						if line == "" {
							continue
						}
						if line[0:2] == "//" {
							continue
						}
						pos := strings.Index(line, ":")
						key := line[0:pos]
						val := line[pos+1:]
						postBody[key] = val
					}
				}
			}

			restyInstance := resty.New()
			rqClient := restyInstance.R()
			rqClient.SetHeaders(reqHeaders)

			var respRet *resty.Response
			if reqmethod == "GET" {
				var err error
				respRet, err = rqClient.Get(apiuri)
				if err != nil {
					resp.ReturnError(http.StatusOK, 0, "请求响应错误！")
					return
				}
			}
			if reqmethod == "POST" {
				if bodytype == "raw" {
					rqClient.SetBody(reqBodyRaw)
				} else {
					// Form data for all request. Typically used with POST and PUT
					rqClient.SetFormData(postBody)
				}
				var err error
				respRet, err = rqClient.Post(apiuri)
				if err != nil {
					resp.ReturnError(http.StatusOK, 0, "请求响应错误！尝试切换请求方式")
					return
				}
			}

			//fmt.Println(respRet)
			//fmt.Println(respRet.StatusCode())
			//fmt.Println(respRet.Time())
			//fmt.Println(respRet.ReceivedAt())
			//fmt.Println(respRet.Header())

			//fmt.Println(reqHeaders)
			//fmt.Println(reqBodyRaw)
			headerStr := ""
			for k, v := range respRet.Header() {
				if headerStr == "" {
					headerStr = k+": "+v[0]
				} else {
					headerStr = headerStr+"<br>" +k+": "+v[0]
				}
			}

			sendResult := new(models.SendResult)
			sendResult.Status = respRet.StatusCode()
			sendResult.Extime = respRet.Time().String()
			sendResult.Header = headerStr
			sendResult.Raw = respRet.String()

			//resultMap := make(map[string]interface{})
			//resultMap["aid"] = aid

			resp.ReturnSuccess(sendResult, "")
			return

		}

	}
	resp.ReturnError(http.StatusOK, 0, "操作失败")
	return
}

func apictrlApidata(w http.ResponseWriter, r *http.Request) {
	r.ParseForm()
	resp := utils.NewResponse(w, r)

	if r.Method == "OPTIONS" {
		resp.DealOptionReq()
		return
	}

	aid, _ := strconv.ParseInt(r.Form.Get("aid"), 10, 64)

	//fmt.Println(aid)

	ormRec := new(models.ApiResource)
	ormRec.Id = aid
	models.MyOrm.Where("id = ?", ormRec.Id).First(ormRec)

	if ormRec.Id <=0 {
		resp.ReturnError(http.StatusOK, 0, "记录不存在")
		return
	}

	var jsoner = jsoniter.ConfigCompatibleWithStandardLibrary
	jsoner.Unmarshal([]byte(ormRec.Rawdata), ormRec)

	ormRec.Rawdata = ""
	resp.ReturnSuccess(ormRec, "操作成功")
	return
}

func apictrlCategory(w http.ResponseWriter, r *http.Request) {
	r.ParseForm()
	resp := utils.NewResponse(w, r)

	if r.Method == "OPTIONS" {
		resp.DealOptionReq()
		return
	}
	//resp.ReturnSuccess(nil, "操作成功!!!!")
	//return

	// 处理POST提交
	if r.Method == "POST" {
		fields := [...]string{"act", "projectid", "cateid"}
		mapFields := make(map[string]string)

		for _, v := range fields {
			mapFields[v] = r.Form.Get(v)
			if mapFields[v] == "" {
				//fmt.Println(v)
				resp.ReturnError(http.StatusOK, 0, "参数错误")
				return
			}
		}

		act := mapFields["act"]
		projectid, _ := strconv.Atoi(mapFields["projectid"])
		cateid, _ := strconv.ParseInt(mapFields["cateid"], 10, 64)

		if act == "save" {
			name := r.Form.Get("name")
			if name == "" {
				resp.ReturnError(http.StatusOK, 0, "参数错误")
				return
			}

			if cateid <= 0 {
				apiCate := new(models.ApiCategory)
				apiCate.Pid = projectid
				apiCate.Name = name

				err := models.MyOrm.Create(apiCate).Error
				if err != nil {
					resp.ReturnError(http.StatusOK, 0, "发生内部错误")
					return
				}
				resp.ReturnSuccess(nil, "操作成功")
				return
			} else {
				apiCate := new(models.ApiCategory)
				apiCate.Id = cateid

				dbQuery := models.MyOrm.Where("id = ?", apiCate.Id).First(apiCate)
				apiCate.Name = name

				err := dbQuery.Updates(apiCate).Error
				if err != nil {
					resp.ReturnError(http.StatusOK, 0, "操作失败")
					return
				}
				resp.ReturnSuccess(nil, "操作成功")
				return
			}
		}

		if act == "del" {
			apiCate := new(models.ApiCategory)
			apiCate.Id = cateid
			models.MyOrm.Where("id = ?", apiCate.Id).Delete(apiCate)

			resp.ReturnSuccess(nil, "操作成功")
			return
		}
		resp.ReturnError(http.StatusOK, 0, "操作失败")
		return
	}

	projectid, _ := strconv.ParseInt(r.Form.Get("projectid"), 10, 64)
	keyword := strings.TrimSpace(r.Form.Get("keyword"))

	apiProject := new(models.ApiCategory)
	apiProject.Id = projectid

	models.MyOrm.Where("id = ?", apiProject.Id).First(apiProject)

	if apiProject.Id <=0 {
		resp.ReturnError(http.StatusOK, 0, "记录不存在")
		return
	}

	var subCategorys []*models.ApiCategory
	models.MyOrm.Where("pid = ?", apiProject.Id).Find(&subCategorys)

	var cateids []int64
	for _, v := range subCategorys {
		cateids = append(cateids, v.Id)
	}

	var apiRecs []*models.ApiResource
	dbQuery := models.MyOrm.Select("id,projectid,cateid,apiname,apiuri,reqmethod,createtime,updatetime")
	dbQuery = dbQuery.Where("cateid IN (?)", cateids)
	if keyword != "" {
		dbQuery = dbQuery.Where("apiname LIKE ? OR apiuri LIKE ?", "%"+keyword+"%", "%"+keyword+"%")
	}
	dbQuery.Order("updatetime DESC").Find(&apiRecs)

	apisAssoc := make(map[int64][]*models.ApiResource)
	for _, v := range apiRecs {
		apisAssoc[int64(v.Cateid)] = append(apisAssoc[int64(v.Cateid)], v)
	}

	emptyArr := []*models.ApiResource{}
	for _, v := range subCategorys {
		dlists, ok := apisAssoc[v.Id]
		if !ok {
			v.Dlists = emptyArr
			continue
		}
		v.Dlists = dlists
	}

	apiProjectTree := new(models.ApiProjectTree)
	apiProjectTree.Project = apiProject
	apiProjectTree.Subcates = subCategorys

	resp.ReturnSuccess(apiProjectTree, "操作成功")
	return
}

func apictrlOperate(w http.ResponseWriter, r *http.Request) {
	r.ParseForm()
	resp := utils.NewResponse(w, r)

	if r.Method == "OPTIONS" {
		resp.DealOptionReq()
		return
	}

	act := r.Form.Get("act")
	aid, _ := strconv.ParseInt(r.Form.Get("aid"), 10, 64)

	//fmt.Println(act)
	//fmt.Println(aid)

	ok := utils.InArray(act, []string{"copy", "del"})
	if !ok {
		resp.ReturnError(http.StatusOK, 0, "参数错误")
		return
	}

	ormRec := new(models.ApiResource)
	ormRec.Id = aid
	models.MyOrm.Where("id = ?", ormRec.Id).First(ormRec)

	if ormRec.Id <=0 {
		resp.ReturnError(http.StatusOK, 0, "记录不存在")
		return
	}

	if (act == "del") {
		models.MyOrm.Where("id = ?", ormRec.Id).Delete(ormRec)
		resp.ReturnSuccess(nil, "操作成功")
		return
	}

	if (act == "copy") {
		ormRec.Id = 0
		ormRec.Apiname = ormRec.Apiname+" Copy"
		err := models.MyOrm.Create(ormRec).Error
		if err != nil {
			resp.ReturnError(http.StatusOK, 0, "发生内部错误")
			return
		}
		resp.ReturnSuccess(nil, "操作成功")
		return
	}
}