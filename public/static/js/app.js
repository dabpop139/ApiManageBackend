webpackJsonp([1],{"4/hK":function(e,t){},NHnr:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var s=a("7+uW"),i=a("fZjL"),o=a.n(i),r=a("u2KI"),l=a.n(r),n=a("mvHQ"),c=a.n(n),d=a("mw3O"),p=a.n(d),u=a("zL8q"),h=a("mtWM"),m={newAxios:function(){var e=localStorage.getItem("uticket")?localStorage.getItem("uticket"):"";return h.create({headers:{"X-Requested-With":"XMLHttpRequest","X-User-Token":e}})},doAjax:function(e,t,a,s,i){var o,r=e.url,l=e.data,n={};o={"X-Requested-With":"XMLHttpRequest","X-User-Token":localStorage.getItem("uticket")?localStorage.getItem("uticket"):""},n.headers=o;var c=null;if("GET"==t&&(c=h.get(r,{params:l,headers:o})),"POST"==t&&("json"==e.ctype?(n.headers["Content-Type"]="application/json; charset=UTF-8",c=h.post(r,l,n)):(n.headers["Content-Type"]="application/x-www-form-urlencoded; charset=UTF-8",c=h.post(r,p.a.stringify(l),n))),!0===i)return c;c&&c.then(function(e){return 4007==e.data.code?(u.MessageBox.alert("请登录后操作!!!","错误 :-(",{}),localStorage.removeItem("uticket"),u.Message.closeAll(),!1):4008==e.data.code?(u.MessageBox.alert("你没有权限访问","错误 :-(",{}),u.Message.closeAll(),!1):("function"!=typeof a||!1!==a(e))&&(u.Message.closeAll(),1==e.data.code?(Object(u.MessageBox)("",""!==e.data.msg?e.data.msg:"操作成功",{}),!0):void u.MessageBox.alert(e.data.msg,"错误 :-(",{}))}).catch(function(e){if("function"==typeof s&&!1===s(e))return!1;if(u.Message.closeAll(),void 0!==e.response){if(401==e.response.status)return u.MessageBox.alert("请登录后操作!!!","错误 :-(",{}),localStorage.removeItem("uticket"),!1;if(403==e.response.status)return u.MessageBox.alert("你没有权限访问","错误 :-(",{}),!1}u.MessageBox.alert("服务器发生错误，请联系管理员","错误 :-(",{})})}},v=a("E5Az"),b=a("R0ti"),f=a.n(b),g=(a("4/hK"),a("Z6qg"),a("5IAE"),a("7Xsf"),{name:"app",components:{elContainer:u.Container,elHeader:u.Header,elAside:u.Aside,elMain:u.Main,elRow:u.Row,elCol:u.Col,elMenu:u.Menu,elSubmenu:u.Submenu,elMenuItemGroup:u.MenuItemGroup,elMenuItem:u.MenuItem,elDialog:u.Dialog,elTabs:u.Tabs,elTabPane:u.TabPane,elCard:u.Card,elCollapse:u.Collapse,elCollapseItem:u.CollapseItem,elDropdown:u.Dropdown,elDropdownMenu:u.DropdownMenu,elDropdownItem:u.DropdownItem,elButton:u.Button,elButtonGroup:u.ButtonGroup,elInput:u.Input,elAutocomplete:u.Autocomplete,elSelect:u.Select,elOption:u.Option,elCheckbox:u.Checkbox,elTag:u.Tag,codemirror:v.codemirror,JsonViewer:f.a},provide:function(){return{reload:this.reload}},data:function(){return{isRouterAlive:!0,fullscreenLoading:!1,cateOpeneds:[],keyword:"",mainTabsCurr:"1",editableTabs:[{aid:0,title:"新建接口",name:"1"}],tabIndex:1,catalogDialogVisible:!1,cmOptions:{tabSize:4,mode:"javascript",lineNumbers:!0,line:!0,lineWrapping:!0,keyMap:"sublime"},cacheApiIds:{},editCatalogId:0,editCatalogName:"",editCatalogOrd:0,editCatalogAct:"add",subCates:{},projectlist:[],projectid:"",cateid:"0",aid:0,apiname:"",reqscheme:"HTTP",apiuri:"",reqmethod:"POST",bodytype:"x-www-form-urlencoded",bodyrawtype:"json",panelActive:"request",collapseAcitves:["req-header","req-body"],reqHeaderChk:!0,reqBodyChk:!0,reqHeader:"",reqBody:"",showRespHeaders:!1,respStatus:"",respExtime:"",respHeader:"",respData:"",respDataJson:null,respDataViewMode:"raw",apiuriSuggesData:[]}},methods:{projectChange:function(){localStorage.setItem("projectid",this.projectid),this.fetchCates()},addTab:function(e){var t=this,a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;if(0!=a){var s=!1;if(this.editableTabs.forEach(function(e,i){if(e.aid===a)return s=!0,void(t.mainTabsCurr=e.name)}),s)return void this.switchTab()}var i=++this.tabIndex+"";this.editableTabs.push({aid:a,title:e,name:i}),this.mainTabsCurr=i},removeTab:function(e){var t=this.editableTabs,a=this.mainTabsCurr;a===e&&t.forEach(function(s,i){if(s.name===e){var o=t[i+1]||t[i-1];o&&(a=o.name)}}),this.mainTabsCurr=a,this.editableTabs=t.filter(function(t){return t.name!==e}),this.switchTab()},switchTab:function(){var e=this;this.editableTabs.forEach(function(t,a){t.name!==e.mainTabsCurr||(0==t.aid?(e.resetRequest(),e.resetRespone()):e.openLink(t.aid,!1))})},catalogOperate:function(e){var t=e[0],a=e[1],s=e[2],i=e[3];this.editCatalogId=t,this.editCatalogName=a,this.editCatalogOrd=s,this.editCatalogAct=i,"del"!=i?this.catalogDialogVisible=!0:this.catalogActHandle(i)},catalogDialogAct:function(e){"cancel"!=e?"add"!=this.editCatalogAct&&"edit"!=this.editCatalogAct||this.catalogActHandle("save"):this.catalogDialogVisible=!1},catalogActHandle:function(e){var t=this,a=this.projectid,s=this.editCatalogId,i=this.editCatalogName,o=this.editCatalogOrd;"del"==e||""!=i.trim()?m.doAjax({url:this.$configSite.apicomm+"apictrl/category",data:{projectid:a,cateid:s,act:e,name:i,ord:o}},"POST",function(e){t.catalogDialogVisible=!1;var a=e.data;return 1==a.code?(t.fetchCates(),u.Message.success(""!==a.msg?a.msg:"操作成功")):u.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,!1},function(e){return t.catalogDialogVisible=!1,!0}):u.Message.warning("分类名称不能为空")},apiItemOperate:function(e){var t=this,a=e[0],s=e[1];m.doAjax({url:this.$configSite.apicomm+"apictrl/operate",data:{aid:a,act:s}},"POST",function(e){var a=e.data;return 1==a.code?t.fetchCates():u.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,!1},function(e){return!0})},openLink:function(e){var t=this,a=!(arguments.length>1&&void 0!==arguments[1])||arguments[1],s=u.Loading.service({background:"rgb(255 255 255 / 0)"}),i=localStorage.getItem("apicache-"+e);if(i){var o=JSON.parse(i);return this.setTabElemData(o,a),void s.close()}m.doAjax({url:this.$configSite.apicomm+"apictrl/apidata",data:{aid:e}},"GET",function(i){var o=i.data;if(1==o.code){if(t.cacheApiIds[e]="",localStorage.setItem("cache_api_ids",c()(t.cacheApiIds)),o.data.apiuri_suggesdata=[],void 0!=o.data.extra){var r=JSON.parse(o.data.extra);void 0!=r.apiurl_history&&r.apiurl_history.forEach(function(e,t){o.data.apiuri_suggesdata.push({value:e})}),delete o.data.extra}t.setTabElemData(o.data,a)}else u.MessageBox.alert(o.msg,"错误 :-(",{});return s.close(),o=null,!1},function(e){return s.close(),!0})},searchResult:function(){""==this.keyword&&(this.cateOpeneds=[]),this.fetchCates(this.keyword)},renewTabName:function(){var e=this;this.editableTabs.forEach(function(t,a){t.name!==e.mainTabsCurr||(e.editableTabs[a].title=e.apiname)})},resetRequest:function(){this.cateid="0",this.aid=0,this.apiname="",this.reqscheme="HTTP",this.apiuri="",this.reqmethod="POST",this.bodytype="x-www-form-urlencoded",this.bodyrawtype="json",this.panelActive="request",this.reqHeaderChk=!0,this.reqBodyChk=!0,this.reqHeader="",this.reqBody="",this.apiuriSuggesData=[]},resetRespone:function(){this.showRespHeaders=!1,this.respStatus="",this.respExtime="",this.respHeader="",this.respData="",this.respDataJson=null,this.respDataViewMode="raw"},setTabElemData:function(e,t){var a=this,s={};if(s.id=this.aid,s.projectid=this.projectid,s.cateid=this.cateid,s.apiname=this.apiname,s.reqscheme=this.reqscheme,s.apiuri=this.apiuri,s.reqmethod=this.reqmethod,s.bodytype=this.bodytype,s.bodyrawtype=this.bodyrawtype,s.rheader_chk=this.reqHeaderChk,s.rbody_chk=this.reqBodyChk,s.rheader=this.reqHeader,s.rbody=this.reqBody,s.respraw=this.respData,s.apiuri_suggesdata=this.apiuriSuggesData,localStorage.setItem("apicache-"+this.aid,c()(s)),this.resetRespone(),this.aid=e.id,this.projectid=e.projectid,this.cateid=e.cateid,this.apiname=e.apiname,this.reqscheme=e.reqscheme,this.apiuri=e.apiuri,this.reqmethod=e.reqmethod,this.bodytype=e.bodytype,this.bodyrawtype=e.bodyrawtype,this.reqHeaderChk=e.rheader_chk,this.reqBodyChk=e.rbody_chk,this.reqHeader=e.rheader,this.reqBody=e.rbody,this.respData=e.respraw,this.apiuriSuggesData=e.apiuri_suggesdata,this.respData){var i=this.respData.substring(0,1);if("{"==i||"["==i){var o=null;try{o=JSON.parse(this.respData),l()(o)}catch(e){}this.respDataJson=o}else/<\/\S+>/g.test(this.respData)&&(this.respDataViewMode="preview",setTimeout(function(){a.changeRespDataViewMode("preview")},100))}1==t&&this.addTab(e.apiname,this.aid),this.cmModeSwitch()},createNew:function(){this.resetRequest(),this.resetRespone(),this.addTab("新建接口")},sendHTTP:function(){var e=this;if(""!=this.apiuri.trim()){var t=u.Loading.service({background:"rgb(255 255 255 / 0)"});this.resetRespone(),m.doAjax({url:this.$configSite.apicomm+"apictrl/handle",data:{act:"send",projectid:this.projectid,cateid:this.cateid,aid:this.aid,apiname:this.apiname,reqscheme:this.reqscheme,apiuri:this.apiuri,reqmethod:this.reqmethod,bodytype:this.bodytype,bodyrawtype:this.bodyrawtype,rheader_chk:this.reqHeaderChk,rbody_chk:this.reqBodyChk,rheader:this.reqHeader,rbody:this.reqBody}},"POST",function(a){var s=a.data;if(1==s.code){e.respData=s.data.raw;var i=e.respData.substring(0,1);if("{"==i||"["==i){var o=null;try{o=JSON.parse(e.respData),l()(o)}catch(e){}e.respDataJson=o}else/<\/\S+>/g.test(e.respData)&&(e.respDataViewMode="preview",setTimeout(function(){e.changeRespDataViewMode("preview")},100));e.respStatus=s.data.status,e.respExtime=s.data.extime,e.respHeader=s.data.header}else u.MessageBox.alert(s.msg,"错误 :-(",{});return t.close(),s=null,!1},function(e){return t.close(),!0})}else u.Message.warning("接口地址不能为空")},sendWebSocket:function(){var e=this;if(""!=this.apiuri.trim()){var t=this.apiuri;"/"!=t.substr(t.length-1,1)&&(t+="/"),void 0!==window.webSocketEl?(window.webSocketEl.readyState==WebSocket.OPEN&&window.webSocketEl.url!=t&&(window.webSocketEl.close(4999),window.webSocketEl=new WebSocket(this.apiuri)),window.webSocketEl.readyState==WebSocket.CLOSED&&(window.webSocketEl=new WebSocket(this.apiuri))):window.webSocketEl=new WebSocket(this.apiuri);window.webSocketEl.onopen=function(){window.webSocketEl.send(e.reqBody)},window.webSocketEl.onmessage=function(t){u.Message.success("接收到WebSocket消息"),e.respData=t.data;var a=e.respData.substring(0,1);if("{"==a||"["==a){var s=null;try{s=JSON.parse(e.respData),l()(s)}catch(e){}e.respDataJson=s}else/<\/\S+>/g.test(e.respData)&&(e.respDataViewMode="preview",setTimeout(function(){e.changeRespDataViewMode("preview")},100))},window.webSocketEl.onclose=function(){},window.webSocketEl.onerror=function(){u.Message.warning("服务连接失败")},window.webSocketEl.readyState==WebSocket.OPEN&&window.webSocketEl.send(this.reqBody)}else u.Message.warning("接口地址不能为空")},sendForm:function(){var e=this;this.apiuriSuggesData.forEach(function(t,a){(t.value==e.apiuri||a>10)&&e.apiuriSuggesData.splice(a,1)}),this.apiuriSuggesData.unshift({value:this.apiuri}),"HTTP"==this.reqscheme&&this.sendHTTP(),"WEBSOCKET"==this.reqscheme&&this.sendWebSocket()},saveForm:function(){var e=this;if("0"!=this.cateid)if(""!=this.apiname.trim())if(""!=this.apiuri.trim()){m.doAjax({url:this.$configSite.apicomm+"apictrl/handle",data:{act:"save",projectid:this.projectid,cateid:this.cateid,aid:this.aid,apiname:this.apiname,reqscheme:this.reqscheme,apiuri:this.apiuri,reqmethod:this.reqmethod,bodytype:this.bodytype,bodyrawtype:this.bodyrawtype,rheader_chk:this.reqHeaderChk,rbody_chk:this.reqBodyChk,rheader:this.reqHeader,rbody:this.reqBody}},"POST",function(t){var a=t.data;return 1==a.code?(u.Message.success(""!==a.msg?a.msg:"操作成功"),e.aid=a.data.aid):u.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,e.renewTabName(),e.fetchCates(),!1},function(e){return!0})}else u.Message.warning("接口地址不能为空");else u.Message.warning("接口名称不能为空");else u.Message.warning("请选择一个分类")},fetchCates:function(e){var t=this,a="",s=void 0;void 0!==e&&(a=e),""!=a&&(s=u.Loading.service({background:"rgb(255 255 255 / 0)"})),m.doAjax({url:this.$configSite.apicomm+"apictrl/category",data:{projectid:this.projectid,keyword:a}},"GET",function(e){var i=e.data;if(1==i.code?t.subCates=i.data.subcates:u.MessageBox.alert(i.msg,"错误 :-(",{}),i=null,""!=a){var o=[];t.subCates.forEach(function(e,t){o.push(e.id+"")}),t.cateOpeneds=o,s.close()}return!1},function(e){return""!=a&&s.close(),!0})},queryApiuri:function(e,t){t(this.apiuriSuggesData),setTimeout(function(){t([])},1e4)},cmModeSwitch:function(){},bodytypeChange:function(){this.cmModeSwitch()},switchShowRespHeaders:function(){this.showRespHeaders=!this.showRespHeaders},changeRespDataViewMode:function(e){if("preview"==e){var t=parseInt(this.mainTabsCurr),a=window.document.getElementById("R_iframe"+t).contentDocument||window.document.frames["R_iframe"+t].document;a.body.innerHTML=this.respData,setTimeout(function(){window.document.getElementById("R_iframeVessel"+t).style.height=a.body.scrollHeight+"px"},200)}this.respDataViewMode=e},keyboardEvent:function(e){return 83==e.keyCode&&e.ctrlKey?(this.saveForm(),e.preventDefault(),e.returnValue=!1,!1):13==e.keyCode&&e.ctrlKey?(this.sendForm(),e.preventDefault(),e.returnValue=!1,!1):void 0},reload:function(){this.isRouterAlive=!1,this.$nextTick(function(){this.isRouterAlive=!0})}},beforeMount:function(){var e=this;m.doAjax({url:this.$configSite.apicomm+"apictrl/project"},"GET",function(t){var a=t.data;if(1==a.code){e.projectlist=a.data;var s=localStorage.getItem("projectid");e.projectid=s?parseInt(s):1,e.fetchCates()}else u.MessageBox.alert(a.msg,"错误 :-(",{});return a=null,!1},function(e){return!0});var t=localStorage.getItem("cache_api_ids");if(t){var a=JSON.parse(t);o()(a).forEach(function(e){localStorage.removeItem("apicache-"+e)}),localStorage.removeItem("cache_api_ids")}},mounted:function(){document.addEventListener("keydown",this.keyboardEvent)}}),w={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{attrs:{id:"app"}},[a("el-container",{staticClass:"fheight"},[a("el-header",{staticClass:"eheader"},[a("div",{staticClass:"layout-top"},[a("div",{staticClass:"disflex left-opts"},[a("div",{staticClass:"flex center elogo"},[a("el-button",{attrs:{type:"info",size:"small",icon:"el-icon-location",circle:""}})],1)])])]),e._v(" "),a("el-container",{staticClass:"fheight"},[a("el-aside",{staticClass:"easide",attrs:{width:"230px"}},[a("div",{staticClass:"el-scrollbar"},[a("div",{staticClass:"project-info"},[a("div",{staticClass:"project-title"},[a("el-select",{on:{change:e.projectChange},model:{value:e.projectid,callback:function(t){e.projectid=t},expression:"projectid"}},e._l(e.projectlist,function(e,t){return a("el-option",{key:t,attrs:{label:e.name,value:e.id}})}),1)],1)]),e._v(" "),a("div",{staticClass:"api-searcher"},[a("el-input",{attrs:{placeholder:"搜索 名称 和 URL"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.searchResult(t)}},model:{value:e.keyword,callback:function(t){e.keyword=t},expression:"keyword"}},[a("i",{staticClass:"el-input__icon el-icon-search",attrs:{slot:"prefix"},slot:"prefix"})])],1),e._v(" "),a("div",{staticClass:"mclear"}),e._v(" "),a("div",{staticClass:"api-toolbar"},[a("div",{staticClass:"right-opts"},[a("a",{attrs:{href:"javascript:;"}},[a("i",{staticClass:"el-icon el-icon-folder-add",on:{click:function(t){return e.catalogOperate([0,"",0,"add"])}}})]),e._v(" \n                              "),a("a",{attrs:{href:"javascript:;"},on:{click:e.createNew}},[a("i",{staticClass:"el-icon el-icon-edit"})])]),e._v(" "),a("div",{staticClass:"mclear"})]),e._v(" "),a("div",{staticClass:"api-catalog"},[a("el-menu",{staticClass:"api-catalog-item",attrs:{"default-active":"1","default-openeds":e.cateOpeneds,"text-color":"#A9A9A9","background-color":"#424242","active-text-color":"#FFD04B"}},e._l(e.subCates,function(t,s){return a("el-submenu",{key:s,attrs:{index:t.id+""}},[a("template",{slot:"title"},[a("el-dropdown",{on:{command:e.catalogOperate}},[a("span",{staticClass:"el-dropdown-link"},[a("i",{staticClass:"el-icon-folder"})]),e._v(" "),a("el-dropdown-menu",{staticClass:"api-catalog-dropsele",attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:[t.id,t.name,t.ord,"edit"]}},[e._v("编辑")]),e._v(" "),a("el-dropdown-item",{attrs:{command:[t.id,t.name,t.ord,"del"]}},[e._v("删除")])],1)],1),e._v(" "),a("span",[e._v(e._s(t.name))])],1),e._v(" "),e._l(t.dlists,function(t,i){return a("el-menu-item",{key:i,attrs:{index:s+"-"+t.id}},[a("label",{class:"method "+t.reqmethod},[e._v(e._s(t.reqmethod))]),e._v(" "),a("label",{staticClass:"name",attrs:{title:t.apiname},on:{click:function(a){return e.openLink(t.id)}}},[e._v(e._s(t.apiname))]),e._v(" "),a("label",{staticClass:"setting"},[a("el-dropdown",{attrs:{trigger:"click"},on:{command:e.apiItemOperate}},[a("span",{staticClass:"el-dropdown-link"},[a("i",{staticClass:"el-icon el-icon-more"})]),e._v(" "),a("el-dropdown-menu",{staticClass:"api-catalog-item-dropsele",attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:[t.id,"copy"]}},[e._v("复制")]),e._v(" "),a("el-dropdown-item",{attrs:{command:[t.id,"del"]}},[e._v("删除")])],1)],1)],1)])})],2)}),1)],1),e._v(" "),a("div",{staticClass:"mt-4"},[e._v(" ")]),e._v(" "),a("div",{staticClass:"mt-4"},[e._v(" ")])])]),e._v(" "),a("el-main",{staticClass:"emain"},[a("el-tabs",{attrs:{type:"card",closable:""},on:{"tab-remove":e.removeTab,"tab-click":e.switchTab},model:{value:e.mainTabsCurr,callback:function(t){e.mainTabsCurr=t},expression:"mainTabsCurr"}},e._l(e.editableTabs,function(t,s){return a("el-tab-pane",{key:t.name,attrs:{label:t.title,name:t.name}},[a("div",{staticClass:"main-area panel",attrs:{"elem-index":s}},[a("div",{staticClass:"panel-title"},[a("el-row",[a("el-col",{attrs:{span:18}},[a("div",{staticClass:"project-title"},[a("el-input",{staticClass:"input-with-select",attrs:{size:"small",placeholder:"请输接口名称"},model:{value:e.apiname,callback:function(t){e.apiname=t},expression:"apiname"}},[a("el-select",{attrs:{slot:"prepend",placeholder:"请选择"},slot:"prepend",model:{value:e.reqscheme,callback:function(t){e.reqscheme=t},expression:"reqscheme"}},[a("el-option",{attrs:{label:"HTTP",value:"HTTP"}}),e._v(" "),a("el-option",{attrs:{label:"WEBSOCKET",value:"WEBSOCKET"}})],1)],1)],1)]),e._v(" "),a("el-col",{staticClass:"pl-3",attrs:{span:6}},[e._v("\n                                          分类：\n                                          "),a("el-select",{attrs:{size:"small",placeholder:"请选择"},model:{value:e.cateid,callback:function(t){e.cateid=t},expression:"cateid"}},[a("el-option",{attrs:{label:"请选择",value:"0"}}),e._v(" "),e._l(e.subCates,function(e,t){return a("el-option",{key:t,attrs:{label:e.name,value:e.id}})})],2)],1)],1)],1),e._v(" "),a("div",{staticClass:"panel-content"},[a("div",{staticClass:"request-uri"},[a("el-row",[a("el-col",{attrs:{span:22}},[a("el-autocomplete",{staticClass:"input-with-select",attrs:{"fetch-suggestions":e.queryApiuri,placeholder:"请输入接口地址",spellcheck:"false"},model:{value:e.apiuri,callback:function(t){e.apiuri=t},expression:"apiuri"}},[a("el-select",{attrs:{slot:"prepend",placeholder:"请选择"},slot:"prepend",model:{value:e.reqmethod,callback:function(t){e.reqmethod=t},expression:"reqmethod"}},[a("el-option",{attrs:{label:"POST",value:"POST"}}),e._v(" "),a("el-option",{attrs:{label:"GET",value:"GET"}}),e._v(" "),a("el-option",{attrs:{label:"PUT",value:"PUT"}}),e._v(" "),a("el-option",{attrs:{label:"PATCH",value:"PATCH"}}),e._v(" "),a("el-option",{attrs:{label:"DELETE",value:"DELETE"}}),e._v(" "),a("el-option",{attrs:{label:"COPY",value:"COPY"}}),e._v(" "),a("el-option",{attrs:{label:"HEAD",value:"HEAD"}}),e._v(" "),a("el-option",{attrs:{label:"OPTIONS",value:"OPTIONS"}}),e._v(" "),a("el-option",{attrs:{label:"LINK",value:"LINK"}}),e._v(" "),a("el-option",{attrs:{label:"UNLINK",value:"UNLINK"}}),e._v(" "),a("el-option",{attrs:{label:"PROPFIND",value:"PROPFIND"}}),e._v(" "),a("el-option",{attrs:{label:"VIEW",value:"VIEW"}})],1),e._v(" "),a("el-button",{attrs:{slot:"append",icon:"el-icon-s-promotion"},on:{click:e.sendForm},slot:"append"},[e._v("发送")])],1)],1),e._v(" "),a("el-col",{attrs:{span:2}},[a("div",{staticClass:"pl-2"},[a("el-button",{attrs:{icon:"el-icon-s-claim"},on:{click:e.saveForm}},[e._v("保存")])],1)]),e._v(" "),a("div",{staticClass:"mclear"})],1)],1),e._v(" "),a("el-tabs",{staticClass:"request-payload mt-1",model:{value:e.panelActive,callback:function(t){e.panelActive=t},expression:"panelActive"}},[a("el-tab-pane",{staticClass:"panel-requset",attrs:{label:"模拟",name:"request"}},[a("div",{staticClass:"box01"},[a("div",{staticClass:"v1"},[a("el-checkbox",{attrs:{label:"Header"},model:{value:e.reqHeaderChk,callback:function(t){e.reqHeaderChk=t},expression:"reqHeaderChk"}}),e._v(" "),a("el-checkbox",{attrs:{label:"Body"},model:{value:e.reqBodyChk,callback:function(t){e.reqBodyChk=t},expression:"reqBodyChk"}})],1),e._v(" "),a("div",{staticClass:"v2"},[a("el-select",{attrs:{size:"mini",placeholder:"请选择"},model:{value:e.bodytype,callback:function(t){e.bodytype=t},expression:"bodytype"}},[a("el-option",{attrs:{label:"x-www-form-urlencoded",value:"x-www-form-urlencoded"}}),e._v(" "),a("el-option",{attrs:{label:"form-data",value:"form-data"}}),e._v(" "),a("el-option",{attrs:{label:"raw",value:"raw"}})],1)],1),e._v(" "),a("div",{staticClass:"mclear"})]),e._v(" "),a("el-collapse",{staticClass:"collapse-box",model:{value:e.collapseAcitves,callback:function(t){e.collapseAcitves=t},expression:"collapseAcitves"}},[e.reqHeaderChk?a("el-collapse-item",{attrs:{name:"req-header"}},[a("template",{slot:"title"},[a("el-tag",{attrs:{size:"medium"}},[e._v("Header")])],1),e._v(" "),a("div",[a("el-input",{attrs:{type:"textarea",autosize:{minRows:4,maxRows:100},spellcheck:"false",placeholder:"请输入内容"},model:{value:e.reqHeader,callback:function(t){e.reqHeader=t},expression:"reqHeader"}})],1)],2):e._e(),e._v(" "),e.reqBodyChk?a("el-collapse-item",{staticClass:"req-body",attrs:{name:"req-body"}},[a("template",{slot:"title"},[a("el-tag",{staticClass:"mr-2",attrs:{size:"medium",type:"success"}},[e._v("Body")]),e._v(" "),"raw"==e.bodytype?a("el-select",{attrs:{size:"mini",placeholder:"请选择"},on:{change:e.bodytypeChange},model:{value:e.bodyrawtype,callback:function(t){e.bodyrawtype=t},expression:"bodyrawtype"}},[a("el-option",{attrs:{label:"JSON (application/json)",value:"json"}}),e._v(" "),a("el-option",{attrs:{label:"XML (text/xml)",value:"xml"}}),e._v(" "),a("el-option",{attrs:{label:"JavaScript (application/javascript)",value:"javascript"}}),e._v(" "),a("el-option",{attrs:{label:"TEXT (text/plain)",value:"plain"}}),e._v(" "),a("el-option",{attrs:{label:"HTML (text/html)",value:"html"}}),e._v(" "),a("el-option",{attrs:{label:"TEXT",value:"text"}})],1):e._e()],1),e._v(" "),a("div",[a("codemirror",{attrs:{options:e.cmOptions,height:"6"},model:{value:e.reqBody,callback:function(t){e.reqBody=t},expression:"reqBody"}})],1)],2):e._e()],1)],1),e._v(" "),a("el-tab-pane",{staticClass:"panel-docs",attrs:{label:"文档",name:"docs"}},[a("div",{staticClass:"mt-1"},[e._v("接口字段文档")])])],1),e._v(" "),a("div",{staticClass:"request-response"},[a("el-card",{staticClass:"box-card"},[a("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[a("span",[e._v("Response")]),e._v(" "),e.respStatus?a("el-tag",{staticClass:"ml-2",attrs:{size:"medium",type:"warning"}},[e._v("Status:"+e._s(e.respStatus))]):e._e(),e._v(" "),e.respExtime?a("el-tag",{staticClass:"ml-1",attrs:{size:"medium"}},[e._v("Time:"+e._s(e.respExtime)+"ms")]):e._e(),e._v(" "),e.respHeader?a("el-tag",{staticClass:"ml-1 sbtn",attrs:{size:"medium",type:"info"},on:{click:e.switchShowRespHeaders}},[e._v("Headers")]):e._e()],1),e._v(" "),e.showRespHeaders?e._e():a("div",{staticClass:"result"},[e.respDataJson?a("json-viewer",{attrs:{value:e.respDataJson,"expand-depth":5,copyable:""}}):e._e(),e._v(" "),e.respDataJson?e._e():a("div",[a("div",{staticClass:"response-datatext-cbtn"},[a("el-button-group",[a("el-button",{attrs:{plain:"",size:"mini"},on:{click:function(t){return e.changeRespDataViewMode("raw")}}},[e._v("Raw")]),e._v(" "),a("el-button",{attrs:{plain:"",size:"mini"},on:{click:function(t){return e.changeRespDataViewMode("preview")}}},[e._v("Preview")])],1)],1),e._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:"raw"==e.respDataViewMode,expression:"respDataViewMode=='raw'"}],staticClass:"response-datatext"},[e._v(e._s(e.respData))]),e._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:"preview"==e.respDataViewMode,expression:"respDataViewMode=='preview'"}],staticClass:"response-datatext",attrs:{id:"R_iframeVessel"+t.name}},[a("iframe",{staticClass:"R_iframe",attrs:{id:"R_iframe"+t.name,name:"R_iframe",width:"100%",height:"100%",src:"about:blank",frameborder:"0",seamless:""}})])])],1),e._v(" "),e.showRespHeaders?a("div",{staticClass:"result"},[a("div",{staticClass:"response-headers",domProps:{innerHTML:e._s(e.respHeader)}})]):e._e()])],1)],1)])])}),1)],1),e._v(" "),a("el-dialog",{attrs:{title:"分类名称",width:"20%",visible:e.catalogDialogVisible},on:{"update:visible":function(t){e.catalogDialogVisible=t}}},[a("div",{staticClass:"el-row"},[a("div",{staticClass:"el-col el-col-14"},[a("el-input",{attrs:{size:"small",autocomplete:"off"},model:{value:e.editCatalogName,callback:function(t){e.editCatalogName=t},expression:"editCatalogName"}})],1),e._v(" "),a("div",{staticClass:"el-col el-col-4"},[a("div",{staticClass:"pl-3 pt-1"},[e._v("排序：")])]),e._v(" "),a("div",{staticClass:"el-col el-col-6"},[a("el-input",{attrs:{size:"small",autocomplete:"off"},model:{value:e.editCatalogOrd,callback:function(t){e.editCatalogOrd=t},expression:"editCatalogOrd"}})],1)]),e._v(" "),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(t){return e.catalogDialogAct("cancel")}}},[e._v("取 消")]),e._v(" "),a("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(t){return e.catalogDialogAct("submit")}}},[e._v("确 定")])],1)])],1)],1)],1)},staticRenderFns:[]};var C=a("VU/8")(g,w,!1,function(e){a("sS2B")},null,null).exports,y=(a("tvR6"),{data:function(){return{homeTop:0}},methods:{},beforeMount:function(){},activated:function(){document.getElementById("content-container").scrollTop=this.homeTop||0},beforeRouteLeave:function(e,t,a){this.homeTop=document.getElementById("content-container").scrollTop||0,a()}}),_={render:function(){var e=this.$createElement;return(this._self._c||e)("div",{staticClass:"container",attrs:{id:"content-container"}},[this._v("\r\n    AAAA\r\n")])},staticRenderFns:[]},k=[{path:"/",component:a("VU/8")(y,_,!1,null,null,null).exports,meta:{title:"",keepAlive:!0}}],T=a("/ocq"),S={apicomm:"http://localhost:8044/app.php/api/",install:function(e){e.prototype.$configSite=S}},q=S;s.default.use(T.a),window.$configSite=q,s.default.use(q);var x=new T.a({routes:k});s.default.config.productionTip=!1,x.beforeEach(function(e,t,a){e.matched.some(function(e){return e.meta.requireAuth})||a()}),x.afterEach(function(e){}),new s.default({el:"#app-box",router:x,components:{App:C},template:"<App/>"})},sS2B:function(e,t){},tvR6:function(e,t){}},["NHnr"]);
//# sourceMappingURL=app.js.map