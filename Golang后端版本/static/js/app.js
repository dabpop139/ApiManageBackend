webpackJsonp([1],{"4/hK":function(e,t){},NHnr:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=a("7+uW"),s=a("fZjL"),o=a.n(s),r=a("mvHQ"),l=a.n(r),n=a("mw3O"),c=a.n(n),d=a("zL8q"),p=a("mtWM"),u={newAxios:function(){var e=localStorage.getItem("uticket")?localStorage.getItem("uticket"):"";return p.create({headers:{"X-Requested-With":"XMLHttpRequest","X-User-Token":e}})},doAjax:function(e,t,a,i,s){var o,r=e.url,l=e.data,n={};o={"X-Requested-With":"XMLHttpRequest","X-User-Token":localStorage.getItem("uticket")?localStorage.getItem("uticket"):""},n.headers=o;var u=null;if("GET"==t&&(u=p.get(r,{params:l,headers:o})),"POST"==t&&("json"==e.ctype?(n.headers["Content-Type"]="application/json; charset=UTF-8",u=p.post(r,l,n)):(n.headers["Content-Type"]="application/x-www-form-urlencoded; charset=UTF-8",u=p.post(r,c.a.stringify(l),n))),!0===s)return u;u&&u.then(function(e){return 4007==e.data.code?(d.MessageBox.alert("请登录后操作!!!","错误 :-(",{}),localStorage.removeItem("uticket"),d.Message.closeAll(),!1):4008==e.data.code?(d.MessageBox.alert("你没有权限访问","错误 :-(",{}),d.Message.closeAll(),!1):("function"!=typeof a||!1!==a(e))&&(d.Message.closeAll(),1==e.data.code?(Object(d.MessageBox)("",""!==e.data.msg?e.data.msg:"操作成功",{}),!0):void d.MessageBox.alert(e.data.msg,"错误 :-(",{}))}).catch(function(e){if("function"==typeof i&&!1===i(e))return!1;if(d.Message.closeAll(),void 0!==e.response){if(401==e.response.status)return d.MessageBox.alert("请登录后操作!!!","错误 :-(",{}),localStorage.removeItem("uticket"),!1;if(403==e.response.status)return d.MessageBox.alert("你没有权限访问","错误 :-(",{}),!1}d.MessageBox.alert("服务器发生错误，请联系管理员","错误 :-(",{})})}},h=a("u2KI"),m=a.n(h),v=a("pFYg"),b=a.n(v),f=a("aFK5"),g=a.n(f),w=function e(t){return g()(t).forEach(function(a){var i=t[a];"object"==(void 0===i?"undefined":b()(i))&&null!==i&&e(i)}),m()(t)},C=a("E5Az"),y=a("R0ti"),_=a.n(y),k=(a("4/hK"),a("Z6qg"),a("5IAE"),a("7Xsf"),{name:"app",components:{elContainer:d.Container,elHeader:d.Header,elAside:d.Aside,elMain:d.Main,elRow:d.Row,elCol:d.Col,elMenu:d.Menu,elSubmenu:d.Submenu,elMenuItemGroup:d.MenuItemGroup,elMenuItem:d.MenuItem,elDialog:d.Dialog,elTabs:d.Tabs,elTabPane:d.TabPane,elCard:d.Card,elCollapse:d.Collapse,elCollapseItem:d.CollapseItem,elDropdown:d.Dropdown,elDropdownMenu:d.DropdownMenu,elDropdownItem:d.DropdownItem,elButton:d.Button,elButtonGroup:d.ButtonGroup,elInput:d.Input,elAutocomplete:d.Autocomplete,elSelect:d.Select,elOption:d.Option,elCheckbox:d.Checkbox,elTag:d.Tag,codemirror:C.codemirror,JsonViewer:_.a},provide:function(){return{reload:this.reload}},data:function(){return{isRouterAlive:!0,fullscreenLoading:!1,cateOpeneds:[],keyword:"",mainTabsCurr:"1",editableTabs:[{aid:0,title:"新建接口",name:"1"}],tabIndex:1,catalogDialogVisible:!1,cmOptions:{tabSize:4,mode:"javascript",lineNumbers:!0,line:!0,lineWrapping:!0,keyMap:"sublime"},cacheApiIds:{},editCatalogId:0,editCatalogName:"",editCatalogOrd:0,editCatalogAct:"add",subCates:{},projectlist:[],projectid:"",cateid:"0",aid:0,apiname:"",reqscheme:"HTTP",apiuri:"",reqmethod:"POST",bodytype:"x-www-form-urlencoded",bodyrawtype:"json",panelActive:"request",collapseAcitves:["req-header","req-body"],reqHeaderChk:!0,reqBodyChk:!0,reqHeader:"",reqBody:"",showRespHeaders:!1,respStatus:"",respExtime:"",respHeader:"",respData:"",respDataJson:null,respDataViewMode:"raw",apiuriSuggesData:[],apiuriSuggesTimer:0}},methods:{projectChange:function(){localStorage.setItem("projectid",this.projectid),this.fetchCates()},addTab:function(e){var t=this,a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;if(0!=a){var i=!1;if(this.editableTabs.forEach(function(e,s){if(e.aid===a)return i=!0,void(t.mainTabsCurr=e.name)}),i)return void this.switchTab()}var s=++this.tabIndex+"";this.editableTabs.push({aid:a,title:e,name:s}),this.mainTabsCurr=s},removeTab:function(e){var t=this.editableTabs,a=this.mainTabsCurr;a===e&&t.forEach(function(i,s){if(i.name===e){var o=t[s+1]||t[s-1];o&&(a=o.name)}}),this.mainTabsCurr=a,this.editableTabs=t.filter(function(t){return t.name!==e}),this.switchTab()},switchTab:function(){var e=this;this.editableTabs.forEach(function(t,a){t.name!==e.mainTabsCurr||(0==t.aid?(e.resetRequest(),e.resetRespone()):e.openLink(t.aid,!1))})},catalogOperate:function(e){var t=e[0],a=e[1],i=e[2],s=e[3];this.editCatalogId=t,this.editCatalogName=a,this.editCatalogOrd=i,this.editCatalogAct=s,"del"!=s?this.catalogDialogVisible=!0:this.catalogActHandle(s)},catalogDialogAct:function(e){"cancel"!=e?"add"!=this.editCatalogAct&&"edit"!=this.editCatalogAct||this.catalogActHandle("save"):this.catalogDialogVisible=!1},catalogActHandle:function(e){var t=this,a=this.projectid,i=this.editCatalogId,s=this.editCatalogName,o=this.editCatalogOrd;"del"==e||""!=s.trim()?u.doAjax({url:this.$configSite.apicomm+"apictrl/category",data:{projectid:a,cateid:i,act:e,name:s,ord:o}},"POST",function(e){t.catalogDialogVisible=!1;var a=e.data;return 1==a.code?(t.fetchCates(),d.Message.success(""!==a.msg?a.msg:"操作成功")):d.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,!1},function(e){return t.catalogDialogVisible=!1,!0}):d.Message.warning("分类名称不能为空")},apiItemOperate:function(e){var t=this,a=e[0],i=e[1];u.doAjax({url:this.$configSite.apicomm+"apictrl/operate",data:{aid:a,act:i}},"POST",function(e){var a=e.data;return 1==a.code?t.fetchCates():d.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,!1},function(e){return!0})},openLink:function(e){var t=this,a=!(arguments.length>1&&void 0!==arguments[1])||arguments[1],i=d.Loading.service({background:"rgb(255 255 255 / 0)"}),s=localStorage.getItem("apicache-"+e);if(s){var o=JSON.parse(s);return this.setTabElemData(o,a),void i.close()}u.doAjax({url:this.$configSite.apicomm+"apictrl/apidata",data:{aid:e}},"GET",function(s){var o=s.data;if(1==o.code){if(t.cacheApiIds[e]="",localStorage.setItem("cache_api_ids",l()(t.cacheApiIds)),o.data.apiuri_suggesdata=[],void 0!=o.data.extra){var r=JSON.parse(o.data.extra);void 0!=r.apiurl_history&&r.apiurl_history.forEach(function(e,t){o.data.apiuri_suggesdata.push({value:e})}),delete o.data.extra}t.setTabElemData(o.data,a)}else d.MessageBox.alert(o.msg,"错误 :-(",{});return i.close(),o=null,!1},function(e){return i.close(),!0})},searchResult:function(){""==this.keyword&&(this.cateOpeneds=[]),this.fetchCates(this.keyword)},renewTabName:function(){var e=this;this.editableTabs.forEach(function(t,a){t.name!==e.mainTabsCurr||(e.editableTabs[a].title=e.apiname)})},resetRequest:function(){this.cateid="0",this.aid=0,this.apiname="",this.reqscheme="HTTP",this.apiuri="",this.reqmethod="POST",this.bodytype="x-www-form-urlencoded",this.bodyrawtype="json",this.panelActive="request",this.reqHeaderChk=!0,this.reqBodyChk=!0,this.reqHeader="",this.reqBody="",this.apiuriSuggesData=[]},resetRespone:function(){this.showRespHeaders=!1,this.respStatus="",this.respExtime="",this.respHeader="",this.respData="",this.respDataJson=null,this.respDataViewMode="raw"},setTabElemData:function(e,t){var a=this,i={};if(i.id=this.aid,i.projectid=this.projectid,i.cateid=this.cateid,i.apiname=this.apiname,i.reqscheme=this.reqscheme,i.apiuri=this.apiuri,i.reqmethod=this.reqmethod,i.bodytype=this.bodytype,i.bodyrawtype=this.bodyrawtype,i.rheader_chk=this.reqHeaderChk,i.rbody_chk=this.reqBodyChk,i.rheader=this.reqHeader,i.rbody=this.reqBody,i.respraw=this.respData,i.apiuri_suggesdata=this.apiuriSuggesData,localStorage.setItem("apicache-"+this.aid,l()(i)),this.resetRespone(),this.aid=e.id,this.projectid=e.projectid,this.cateid=e.cateid,this.apiname=e.apiname,this.reqscheme=e.reqscheme,this.apiuri=e.apiuri,this.reqmethod=e.reqmethod,this.bodytype=e.bodytype,this.bodyrawtype=e.bodyrawtype,this.reqHeaderChk=e.rheader_chk,this.reqBodyChk=e.rbody_chk,this.reqHeader=e.rheader,this.reqBody=e.rbody,this.respData=e.respraw,this.apiuriSuggesData=e.apiuri_suggesdata,this.respData){var s=this.respData.substring(0,1);if("{"==s||"["==s){var o=null;try{o=JSON.parse(this.respData),w(o)}catch(e){}this.respDataJson=o}else/<\/\S+>/g.test(this.respData)&&(this.respDataViewMode="preview",setTimeout(function(){a.changeRespDataViewMode("preview")},100))}1==t&&this.addTab(e.apiname,this.aid),this.cmModeSwitch()},createNew:function(){this.resetRequest(),this.resetRespone(),this.addTab("新建接口")},sendHTTP:function(){var e=this;if(""!=this.apiuri.trim()){var t=d.Loading.service({background:"rgb(255 255 255 / 0)"});this.resetRespone(),u.doAjax({url:this.$configSite.apicomm+"apictrl/handle",data:{act:"send",projectid:this.projectid,cateid:this.cateid,aid:this.aid,apiname:this.apiname,reqscheme:this.reqscheme,apiuri:this.apiuri,reqmethod:this.reqmethod,bodytype:this.bodytype,bodyrawtype:this.bodyrawtype,rheader_chk:this.reqHeaderChk,rbody_chk:this.reqBodyChk,rheader:this.reqHeader,rbody:this.reqBody}},"POST",function(a){var i=a.data;if(1==i.code){e.respData=i.data.raw;var s=e.respData.substring(0,1);if("{"==s||"["==s){var o=null;try{o=JSON.parse(e.respData),w(o)}catch(e){}e.respDataJson=o}else/<\/\S+>/g.test(e.respData)&&(e.respDataViewMode="preview",setTimeout(function(){e.changeRespDataViewMode("preview")},100));e.respStatus=i.data.status,e.respExtime=i.data.extime,e.respHeader=i.data.header}else d.MessageBox.alert(i.msg,"错误 :-(",{});return t.close(),i=null,!1},function(e){return t.close(),!0})}else d.Message.warning("接口地址不能为空")},sendWebSocket:function(){var e=this;if(""!=this.apiuri.trim()){var t=this.apiuri;"/"!=t.substr(t.length-1,1)&&(t+="/"),void 0!==window.webSocketEl?(window.webSocketEl.readyState==WebSocket.OPEN&&window.webSocketEl.url!=t&&(window.webSocketEl.close(4999),window.webSocketEl=new WebSocket(this.apiuri)),window.webSocketEl.readyState==WebSocket.CLOSED&&(window.webSocketEl=new WebSocket(this.apiuri))):window.webSocketEl=new WebSocket(this.apiuri);window.webSocketEl.onopen=function(){window.webSocketEl.send(e.reqBody)},window.webSocketEl.onmessage=function(t){d.Message.success("接收到WebSocket消息"),e.respData=t.data;var a=e.respData.substring(0,1);if("{"==a||"["==a){var i=null;try{i=JSON.parse(e.respData),w(i)}catch(e){}e.respDataJson=i}else/<\/\S+>/g.test(e.respData)&&(e.respDataViewMode="preview",setTimeout(function(){e.changeRespDataViewMode("preview")},100))},window.webSocketEl.onclose=function(){},window.webSocketEl.onerror=function(){d.Message.warning("服务连接失败")},window.webSocketEl.readyState==WebSocket.OPEN&&window.webSocketEl.send(this.reqBody)}else d.Message.warning("接口地址不能为空")},sendForm:function(){var e=this;this.apiuriSuggesData.forEach(function(t,a){(t.value==e.apiuri||a>10)&&e.apiuriSuggesData.splice(a,1)}),this.apiuriSuggesData.unshift({value:this.apiuri}),"HTTP"==this.reqscheme&&this.sendHTTP(),"WEBSOCKET"==this.reqscheme&&this.sendWebSocket()},saveForm:function(){var e=this;if("0"!=this.cateid)if(""!=this.apiname.trim())if(""!=this.apiuri.trim()){u.doAjax({url:this.$configSite.apicomm+"apictrl/handle",data:{act:"save",projectid:this.projectid,cateid:this.cateid,aid:this.aid,apiname:this.apiname,reqscheme:this.reqscheme,apiuri:this.apiuri,reqmethod:this.reqmethod,bodytype:this.bodytype,bodyrawtype:this.bodyrawtype,rheader_chk:this.reqHeaderChk,rbody_chk:this.reqBodyChk,rheader:this.reqHeader,rbody:this.reqBody}},"POST",function(t){var a=t.data;return 1==a.code?(d.Message.success(""!==a.msg?a.msg:"操作成功"),e.aid=a.data.aid):d.MessageBox.alert(a.msg,"错误 :-(",{}),a=null,e.renewTabName(),e.fetchCates(),!1},function(e){return!0})}else d.Message.warning("接口地址不能为空");else d.Message.warning("接口名称不能为空");else d.Message.warning("请选择一个分类")},fetchCates:function(e){var t=this,a="",i=void 0;void 0!==e&&(a=e),""!=a&&(i=d.Loading.service({background:"rgb(255 255 255 / 0)"})),u.doAjax({url:this.$configSite.apicomm+"apictrl/category",data:{projectid:this.projectid,keyword:a}},"GET",function(e){var s=e.data;if(1==s.code?t.subCates=w(s.data.subcates):d.MessageBox.alert(s.msg,"错误 :-(",{}),s=null,""!=a){var o=[];t.subCates.forEach(function(e,t){o.push(e.id+"")}),t.cateOpeneds=o,i.close()}return!1},function(e){return""!=a&&i.close(),!0})},queryApiuri:function(e,t){t(this.apiuriSuggesData),clearTimeout(this.apiuriSuggesTimer),this.apiuriSuggesTimer=setTimeout(function(){t([])},1e4)},cmModeSwitch:function(){},bodytypeChange:function(){this.cmModeSwitch()},switchShowRespHeaders:function(){this.showRespHeaders=!this.showRespHeaders},changeRespDataViewMode:function(e){if("preview"==e){var t=parseInt(this.mainTabsCurr),a=window.document.getElementById("R_iframe"+t).contentDocument||window.document.frames["R_iframe"+t].document;a.body.innerHTML=this.respData,setTimeout(function(){window.document.getElementById("R_iframeVessel"+t).style.height=a.body.scrollHeight+"px"},200)}this.respDataViewMode=e},keyboardEvent:function(e){return 83==e.keyCode&&e.ctrlKey?(this.saveForm(),e.preventDefault(),e.returnValue=!1,!1):13==e.keyCode&&e.altKey?(this.sendForm(),e.preventDefault(),e.returnValue=!1,!1):81==e.keyCode&&e.altKey?(this.removeTab(this.mainTabsCurr),!1):void 0},reload:function(){this.isRouterAlive=!1,this.$nextTick(function(){this.isRouterAlive=!0})}},beforeMount:function(){var e=this;u.doAjax({url:this.$configSite.apicomm+"apictrl/project"},"GET",function(t){var a=t.data;if(1==a.code){e.projectlist=a.data;var i=localStorage.getItem("projectid");e.projectid=i?parseInt(i):1,e.fetchCates()}else d.MessageBox.alert(a.msg,"错误 :-(",{});return a=null,!1},function(e){return!0});var t=localStorage.getItem("cache_api_ids");if(t){var a=JSON.parse(t);o()(a).forEach(function(e){localStorage.removeItem("apicache-"+e)}),localStorage.removeItem("cache_api_ids")}},mounted:function(){document.addEventListener("keydown",this.keyboardEvent)}}),T={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{attrs:{id:"app"}},[a("el-container",{staticClass:"fheight"},[a("el-header",{staticClass:"eheader"},[a("div",{staticClass:"layout-top"},[a("div",{staticClass:"disflex left-opts"},[a("div",{staticClass:"flex center elogo"},[a("el-button",{attrs:{type:"info",size:"small",icon:"el-icon-location",circle:""}})],1)])])]),e._v(" "),a("el-container",{staticClass:"fheight"},[a("el-aside",{staticClass:"easide",attrs:{width:"230px"}},[a("div",{staticClass:"el-scrollbar"},[a("div",{staticClass:"project-info"},[a("div",{staticClass:"project-title"},[a("el-select",{on:{change:e.projectChange},model:{value:e.projectid,callback:function(t){e.projectid=t},expression:"projectid"}},e._l(e.projectlist,function(e,t){return a("el-option",{key:t,attrs:{label:e.name,value:e.id}})}),1)],1)]),e._v(" "),a("div",{staticClass:"api-searcher"},[a("el-input",{attrs:{placeholder:"搜索 名称 和 URL"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.searchResult(t)}},model:{value:e.keyword,callback:function(t){e.keyword=t},expression:"keyword"}},[a("i",{staticClass:"el-input__icon el-icon-search",attrs:{slot:"prefix"},slot:"prefix"})])],1),e._v(" "),a("div",{staticClass:"mclear"}),e._v(" "),a("div",{staticClass:"api-toolbar"},[a("div",{staticClass:"right-opts"},[a("a",{attrs:{href:"javascript:;"}},[a("i",{staticClass:"el-icon el-icon-folder-add",on:{click:function(t){return e.catalogOperate([0,"",0,"add"])}}})]),e._v(" \n                              "),a("a",{attrs:{href:"javascript:;"},on:{click:e.createNew}},[a("i",{staticClass:"el-icon el-icon-edit"})])]),e._v(" "),a("div",{staticClass:"mclear"})]),e._v(" "),a("div",{staticClass:"api-catalog"},[a("el-menu",{staticClass:"api-catalog-item",attrs:{"default-active":"1","default-openeds":e.cateOpeneds,"text-color":"#A9A9A9","background-color":"#424242","active-text-color":"#FFD04B"}},e._l(e.subCates,function(t,i){return a("el-submenu",{key:i,attrs:{index:t.id+""}},[a("template",{slot:"title"},[a("el-dropdown",{on:{command:e.catalogOperate}},[a("span",{staticClass:"el-dropdown-link"},[a("i",{staticClass:"el-icon-folder"})]),e._v(" "),a("el-dropdown-menu",{staticClass:"api-catalog-dropsele",attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:[t.id,t.name,t.ord,"edit"]}},[e._v("编辑")]),e._v(" "),a("el-dropdown-item",{attrs:{command:[t.id,t.name,t.ord,"del"]}},[e._v("删除")])],1)],1),e._v(" "),a("span",[e._v(e._s(t.name))])],1),e._v(" "),e._l(t.dlists,function(t,s){return a("el-menu-item",{key:s,attrs:{index:i+"-"+t.id}},[a("label",{class:"method "+t.reqmethod},[e._v(e._s(t.reqmethod))]),e._v(" "),a("label",{staticClass:"name",attrs:{title:t.apiname},on:{click:function(a){return e.openLink(t.id)}}},[e._v(e._s(t.apiname))]),e._v(" "),a("label",{staticClass:"setting"},[a("el-dropdown",{attrs:{trigger:"click"},on:{command:e.apiItemOperate}},[a("span",{staticClass:"el-dropdown-link"},[a("i",{staticClass:"el-icon el-icon-more"})]),e._v(" "),a("el-dropdown-menu",{staticClass:"api-catalog-item-dropsele",attrs:{slot:"dropdown"},slot:"dropdown"},[a("el-dropdown-item",{attrs:{command:[t.id,"copy"]}},[e._v("复制")]),e._v(" "),a("el-dropdown-item",{attrs:{command:[t.id,"del"]}},[e._v("删除")])],1)],1)],1)])})],2)}),1)],1),e._v(" "),a("div",{staticClass:"mt-4"},[e._v(" ")]),e._v(" "),a("div",{staticClass:"mt-4"},[e._v(" ")])])]),e._v(" "),a("el-main",{staticClass:"emain"},[a("el-tabs",{attrs:{type:"card",closable:""},on:{"tab-remove":e.removeTab,"tab-click":e.switchTab},model:{value:e.mainTabsCurr,callback:function(t){e.mainTabsCurr=t},expression:"mainTabsCurr"}},e._l(e.editableTabs,function(t,i){return a("el-tab-pane",{key:t.name,attrs:{label:t.title,name:t.name}},[a("div",{staticClass:"main-area panel",attrs:{"elem-index":i}},[a("div",{staticClass:"panel-title"},[a("el-row",[a("el-col",{attrs:{span:18}},[a("div",{staticClass:"project-title"},[a("el-input",{staticClass:"input-with-select",attrs:{size:"small",placeholder:"请输接口名称"},model:{value:e.apiname,callback:function(t){e.apiname=t},expression:"apiname"}},[a("el-select",{attrs:{slot:"prepend",placeholder:"请选择"},slot:"prepend",model:{value:e.reqscheme,callback:function(t){e.reqscheme=t},expression:"reqscheme"}},[a("el-option",{attrs:{label:"HTTP",value:"HTTP"}}),e._v(" "),a("el-option",{attrs:{label:"WEBSOCKET",value:"WEBSOCKET"}})],1)],1)],1)]),e._v(" "),a("el-col",{staticClass:"pl-3",attrs:{span:6}},[e._v("\n                                          分类：\n                                          "),a("el-select",{attrs:{size:"small",placeholder:"请选择"},model:{value:e.cateid,callback:function(t){e.cateid=t},expression:"cateid"}},[a("el-option",{attrs:{label:"请选择",value:"0"}}),e._v(" "),e._l(e.subCates,function(e,t){return a("el-option",{key:t,attrs:{label:e.name,value:e.id}})})],2)],1)],1)],1),e._v(" "),a("div",{staticClass:"panel-content"},[a("div",{staticClass:"request-uri"},[a("el-row",[a("el-col",{attrs:{span:22}},[a("el-autocomplete",{staticClass:"input-with-select",attrs:{"fetch-suggestions":e.queryApiuri,placeholder:"请输入接口地址",spellcheck:"false"},model:{value:e.apiuri,callback:function(t){e.apiuri=t},expression:"apiuri"}},[a("el-select",{attrs:{slot:"prepend",placeholder:"请选择"},slot:"prepend",model:{value:e.reqmethod,callback:function(t){e.reqmethod=t},expression:"reqmethod"}},[a("el-option",{attrs:{label:"POST",value:"POST"}}),e._v(" "),a("el-option",{attrs:{label:"GET",value:"GET"}}),e._v(" "),a("el-option",{attrs:{label:"PUT",value:"PUT"}}),e._v(" "),a("el-option",{attrs:{label:"PATCH",value:"PATCH"}}),e._v(" "),a("el-option",{attrs:{label:"DELETE",value:"DELETE"}}),e._v(" "),a("el-option",{attrs:{label:"COPY",value:"COPY"}}),e._v(" "),a("el-option",{attrs:{label:"HEAD",value:"HEAD"}}),e._v(" "),a("el-option",{attrs:{label:"OPTIONS",value:"OPTIONS"}}),e._v(" "),a("el-option",{attrs:{label:"LINK",value:"LINK"}}),e._v(" "),a("el-option",{attrs:{label:"UNLINK",value:"UNLINK"}}),e._v(" "),a("el-option",{attrs:{label:"PROPFIND",value:"PROPFIND"}}),e._v(" "),a("el-option",{attrs:{label:"VIEW",value:"VIEW"}})],1),e._v(" "),a("el-button",{attrs:{slot:"append",icon:"el-icon-s-promotion"},on:{click:e.sendForm},slot:"append"},[e._v("发送")])],1)],1),e._v(" "),a("el-col",{attrs:{span:2}},[a("div",{staticClass:"pl-2"},[a("el-button",{attrs:{icon:"el-icon-s-claim"},on:{click:e.saveForm}},[e._v("保存")])],1)]),e._v(" "),a("div",{staticClass:"mclear"})],1)],1),e._v(" "),a("el-tabs",{staticClass:"request-payload mt-1",model:{value:e.panelActive,callback:function(t){e.panelActive=t},expression:"panelActive"}},[a("el-tab-pane",{staticClass:"panel-requset",attrs:{label:"模拟",name:"request"}},[a("div",{staticClass:"box01"},[a("div",{staticClass:"v1"},[a("el-checkbox",{attrs:{label:"Header"},model:{value:e.reqHeaderChk,callback:function(t){e.reqHeaderChk=t},expression:"reqHeaderChk"}}),e._v(" "),a("el-checkbox",{attrs:{label:"Body"},model:{value:e.reqBodyChk,callback:function(t){e.reqBodyChk=t},expression:"reqBodyChk"}})],1),e._v(" "),a("div",{staticClass:"v2"},[a("el-select",{attrs:{size:"mini",placeholder:"请选择"},model:{value:e.bodytype,callback:function(t){e.bodytype=t},expression:"bodytype"}},[a("el-option",{attrs:{label:"x-www-form-urlencoded",value:"x-www-form-urlencoded"}}),e._v(" "),a("el-option",{attrs:{label:"form-data",value:"form-data"}}),e._v(" "),a("el-option",{attrs:{label:"raw",value:"raw"}})],1)],1),e._v(" "),a("div",{staticClass:"mclear"})]),e._v(" "),a("el-collapse",{staticClass:"collapse-box",model:{value:e.collapseAcitves,callback:function(t){e.collapseAcitves=t},expression:"collapseAcitves"}},[e.reqHeaderChk?a("el-collapse-item",{attrs:{name:"req-header"}},[a("template",{slot:"title"},[a("el-tag",{attrs:{size:"medium"}},[e._v("Header")])],1),e._v(" "),a("div",[a("el-input",{attrs:{type:"textarea",autosize:{minRows:4,maxRows:100},spellcheck:"false",placeholder:"请输入内容"},model:{value:e.reqHeader,callback:function(t){e.reqHeader=t},expression:"reqHeader"}})],1)],2):e._e(),e._v(" "),e.reqBodyChk?a("el-collapse-item",{staticClass:"req-body",attrs:{name:"req-body"}},[a("template",{slot:"title"},[a("el-tag",{staticClass:"mr-2",attrs:{size:"medium",type:"success"}},[e._v("Body")]),e._v(" "),"raw"==e.bodytype?a("el-select",{attrs:{size:"mini",placeholder:"请选择"},on:{change:e.bodytypeChange},model:{value:e.bodyrawtype,callback:function(t){e.bodyrawtype=t},expression:"bodyrawtype"}},[a("el-option",{attrs:{label:"JSON (application/json)",value:"json"}}),e._v(" "),a("el-option",{attrs:{label:"XML (text/xml)",value:"xml"}}),e._v(" "),a("el-option",{attrs:{label:"JavaScript (application/javascript)",value:"javascript"}}),e._v(" "),a("el-option",{attrs:{label:"TEXT (text/plain)",value:"plain"}}),e._v(" "),a("el-option",{attrs:{label:"HTML (text/html)",value:"html"}}),e._v(" "),a("el-option",{attrs:{label:"TEXT",value:"text"}})],1):e._e()],1),e._v(" "),a("div",[a("codemirror",{attrs:{options:e.cmOptions,height:"6"},model:{value:e.reqBody,callback:function(t){e.reqBody=t},expression:"reqBody"}})],1)],2):e._e()],1)],1),e._v(" "),a("el-tab-pane",{staticClass:"panel-docs",attrs:{label:"文档",name:"docs"}},[a("div",{staticClass:"mt-1"},[e._v("接口字段文档")])])],1),e._v(" "),a("div",{staticClass:"request-response"},[a("el-card",{staticClass:"box-card"},[a("div",{staticClass:"clearfix",attrs:{slot:"header"},slot:"header"},[a("span",[e._v("Response")]),e._v(" "),e.respStatus?a("el-tag",{staticClass:"ml-2",attrs:{size:"medium",type:"warning"}},[e._v("Status:"+e._s(e.respStatus))]):e._e(),e._v(" "),e.respExtime?a("el-tag",{staticClass:"ml-1",attrs:{size:"medium"}},[e._v("Time:"+e._s(e.respExtime)+"ms")]):e._e(),e._v(" "),e.respHeader?a("el-tag",{staticClass:"ml-1 sbtn",attrs:{size:"medium",type:"info"},on:{click:e.switchShowRespHeaders}},[e._v("Headers")]):e._e()],1),e._v(" "),e.showRespHeaders?e._e():a("div",{staticClass:"result"},[e.respDataJson?a("json-viewer",{attrs:{value:e.respDataJson,"expand-depth":5,copyable:""}}):e._e(),e._v(" "),e.respDataJson?e._e():a("div",[a("div",{staticClass:"response-datatext-cbtn"},[a("el-button-group",[a("el-button",{attrs:{plain:"",size:"mini"},on:{click:function(t){return e.changeRespDataViewMode("raw")}}},[e._v("Raw")]),e._v(" "),a("el-button",{attrs:{plain:"",size:"mini"},on:{click:function(t){return e.changeRespDataViewMode("preview")}}},[e._v("Preview")])],1)],1),e._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:"raw"==e.respDataViewMode,expression:"respDataViewMode=='raw'"}],staticClass:"response-datatext"},[e._v(e._s(e.respData))]),e._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:"preview"==e.respDataViewMode,expression:"respDataViewMode=='preview'"}],staticClass:"response-datatext",attrs:{id:"R_iframeVessel"+t.name}},[a("iframe",{staticClass:"R_iframe",attrs:{id:"R_iframe"+t.name,name:"R_iframe",width:"100%",height:"100%",src:"about:blank",frameborder:"0",seamless:""}})])])],1),e._v(" "),e.showRespHeaders?a("div",{staticClass:"result"},[a("div",{staticClass:"response-headers",domProps:{innerHTML:e._s(e.respHeader)}})]):e._e()])],1)],1)])])}),1)],1),e._v(" "),a("el-dialog",{attrs:{title:"分类名称",width:"20%",visible:e.catalogDialogVisible},on:{"update:visible":function(t){e.catalogDialogVisible=t}}},[a("div",{staticClass:"el-row"},[a("div",{staticClass:"el-col el-col-14"},[a("el-input",{attrs:{size:"small",autocomplete:"off"},model:{value:e.editCatalogName,callback:function(t){e.editCatalogName=t},expression:"editCatalogName"}})],1),e._v(" "),a("div",{staticClass:"el-col el-col-4"},[a("div",{staticClass:"pl-3 pt-1"},[e._v("排序：")])]),e._v(" "),a("div",{staticClass:"el-col el-col-6"},[a("el-input",{attrs:{size:"small",autocomplete:"off"},model:{value:e.editCatalogOrd,callback:function(t){e.editCatalogOrd=t},expression:"editCatalogOrd"}})],1)]),e._v(" "),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{attrs:{size:"small"},on:{click:function(t){return e.catalogDialogAct("cancel")}}},[e._v("取 消")]),e._v(" "),a("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(t){return e.catalogDialogAct("submit")}}},[e._v("确 定")])],1)])],1)],1)],1)},staticRenderFns:[]};var S=a("VU/8")(k,T,!1,function(e){a("tJcN")},null,null).exports,q=(a("tvR6"),{data:function(){return{homeTop:0}},methods:{},beforeMount:function(){},activated:function(){document.getElementById("content-container").scrollTop=this.homeTop||0},beforeRouteLeave:function(e,t,a){this.homeTop=document.getElementById("content-container").scrollTop||0,a()}}),x={render:function(){var e=this.$createElement;return(this._self._c||e)("div",{staticClass:"container",attrs:{id:"content-container"}},[this._v("\r\n    AAAA\r\n")])},staticRenderFns:[]},D=[{path:"/",component:a("VU/8")(q,x,!1,null,null,null).exports,meta:{title:"",keepAlive:!0}}],E=a("/ocq"),M={apicomm:"http://localhost:8045/app.php/api/",install:function(e){e.prototype.$configSite=M}},A=M;i.default.use(E.a),window.$configSite=A,i.default.use(A);var H=new E.a({routes:D});i.default.config.productionTip=!1,H.beforeEach(function(e,t,a){e.matched.some(function(e){return e.meta.requireAuth})||a()}),H.afterEach(function(e){}),new i.default({el:"#app-box",router:H,components:{App:S},template:"<App/>"})},tJcN:function(e,t){},tvR6:function(e,t){}},["NHnr"]);
//# sourceMappingURL=app.js.map