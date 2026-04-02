import{_ as A,y as D,f as L,o as c,c as s,b as r,w as g,a as t,z as q,T as I,u as P,A as G,B as J,F as T,r as N,C as m,t as u,D as Q,d as K,E as O,G as p,H as W,I as Y,J as x,K as X,L as Z,M as tt,e as M,q as R}from"./index-CEyONJN6.js";const et={class:"ai-chat-wrapper"},at={key:0,class:"chat-window"},dt={class:"chat-header"},nt={class:"header-actions"},ct={key:0,class:"empty-state"},st={key:0,class:"msg-avatar ai"},ot={class:"msg-content"},it={key:0,class:"ai-bubble"},lt={key:0,class:"typing-dots"},rt=["innerHTML"],ut={key:1,class:"user-bubble"},vt={key:2,class:"ai-actions"},pt=["onClick"],bt={key:1,class:"msg-avatar user"},mt={key:0,class:"quick-questions"},ht={class:"quick-btns"},gt={class:"chat-input"},qt={class:"input-row"},wt={key:1},ft={__name:"AiChatWindow",setup(U){const o=x(!1),y=x(""),h=x(!1),i=x([]),$=x(null),v=["如何新增一个用户？","代码生成器怎么用？","工作流如何配置？","多租户模式怎么开启？"],w=()=>{o.value=!o.value},b=()=>{Z(()=>{$.value&&($.value.scrollTop=$.value.scrollHeight)})},e=async l=>{var C;if(!l.trim()||h.value)return;const a={role:"user",content:l.trim()};i.value.push(a),y.value="",h.value=!0,b();const _={role:"ai",content:"",loading:!0};i.value.push(_);try{const f=await fetch("/pcapi/ai/chat",{method:"POST",headers:{"Content-Type":"application/json",Authorization:`Bearer ${localStorage.getItem("token")||""}`},body:JSON.stringify({message:a.content,history:i.value.filter(n=>!n.loading).map(n=>({role:n.role,content:n.content}))})}).catch(()=>null);let k="";if(f&&f.ok){const n=await f.json();k=((C=n==null?void 0:n.data)==null?void 0:C.reply)||(n==null?void 0:n.reply)||""}k||(k=d(a.content)),i.value=i.value.filter(n=>!n.loading),i.value.push({role:"ai",content:k})}catch{i.value=i.value.filter(f=>!f.loading),i.value.push({role:"ai",content:"抱歉，服务暂时不可用，请稍后重试。"})}finally{h.value=!1,b(),F()}},d=l=>{const a=l.toLowerCase();return a.includes("用户")||a.includes("新增")?`**用户管理操作指南**

1. 进入 **系统管理 → 用户管理**
2. 点击 **新增用户** 按钮
3. 填写用户名、密码、昵称
4. 选择所属部门和岗位
5. 分配对应角色
6. 点击 **确定** 保存

如需批量导入，可使用 **代码生成器** 导出模板后批量导入。`:a.includes("代码生成")||a.includes("crud")?`**代码生成器使用流程**

1. 进入 **系统管理 → 代码生成器**
2. 选择要生成的数据表
3. 配置基本信息（作者、模块名）
4. 设置字段映射和验证规则
5. 点击 **生成代码**
6. 下载并解压覆盖到对应目录

生成后会自动创建：Model、Controller、路由、前端API文件。`:a.includes("工作流")||a.includes("审批")?`**工作流配置步骤**

1. 进入 **工作流 → 流程设计**
2. 拖拽节点绘制流程图
3. 配置每个节点的：
   - 审批人（用户/角色/部门）
   - 审批动作（同意/拒绝/转交）
   - 表单字段权限
4. 设置开始节点和结束节点
5. 保存并发布流程

实例管理中可查看所有流程实例和待办任务。`:a.includes("租户")||a.includes("多租户")?`**多租户模式开启**

1. 进入 **系统设置 → 系统配置**
2. 找到 **多租户模式** 配置项
3. 开启开关并保存
4. 进入 **租户管理** 新增租户
5. 为租户分配套餐（基础版/专业版/旗舰版）
6. 各租户数据完全隔离，登录入口独立

建议生产环境开启 Redis 缓存以提升多租户查询性能。`:`感谢你的提问！飞鱼 Admin 是一个功能完备的企业级后台框架。

你可以尝试问我：
- 如何新增用户？
- 代码生成器怎么用？
- 工作流如何配置？
- 多租户模式怎么开启？

或者前往 **技术文档** 页面获取更详细的开发指南。`},E=()=>{e(y.value)},j=l=>{e(l)},B=()=>{i.value=[],localStorage.removeItem("ai_chat_history")},H=l=>{navigator.clipboard.writeText(l).then(()=>{X.success("已复制到剪贴板")})},z=l=>l.replace(/\*\*(.+?)\*\*/g,"<strong>$1</strong>").replace(/\n/g,"<br>").replace(/- (.+)/g,'<span style="display:block;margin-left:8px">• $1</span>'),F=()=>{try{localStorage.setItem("ai_chat_history",JSON.stringify(i.value))}catch{}},V=()=>{try{const l=localStorage.getItem("ai_chat_history");l&&(i.value=JSON.parse(l).filter(a=>!a.loading))}catch{}};return D(()=>{V()}),(l,a)=>{const _=L("el-icon"),C=L("el-tooltip"),f=L("el-button"),k=L("el-input");return c(),s("div",et,[r(I,{name:"float-btn"},{default:g(()=>[o.value?q("",!0):(c(),s("div",{key:0,class:"float-btn",onClick:w,title:"AI助手"},[...a[2]||(a[2]=[t("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none"},[t("path",{d:"M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z",fill:"currentColor"}),t("path",{d:"M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z",fill:"none"}),t("circle",{cx:"9",cy:"13",r:"1.5",fill:"currentColor"}),t("circle",{cx:"12",cy:"13",r:"1.5",fill:"currentColor"}),t("circle",{cx:"15",cy:"13",r:"1.5",fill:"currentColor"})],-1),t("span",{class:"btn-label"},"AI助手",-1)])]))]),_:1}),r(I,{name:"chat-slide"},{default:g(()=>[o.value?(c(),s("div",at,[t("div",dt,[a[3]||(a[3]=t("div",{class:"header-left"},[t("div",{class:"ai-avatar"},[t("svg",{width:"20",height:"20",viewBox:"0 0 48 48",fill:"none"},[t("rect",{width:"48",height:"48",rx:"12",fill:"#2563EB"}),t("path",{d:"M14 24L24 14L34 24L24 34L14 24Z",fill:"#fff"}),t("circle",{cx:"24",cy:"24",r:"5",fill:"#2563EB"})])]),t("div",{class:"header-info"},[t("span",{class:"ai-name"},"飞鱼 AI 助手"),t("span",{class:"ai-status"},"在线")])],-1)),t("div",nt,[r(C,{content:"清空对话",placement:"bottom"},{default:g(()=>[r(_,{onClick:B,class:"action-icon"},{default:g(()=>[r(P(G))]),_:1})]),_:1}),r(_,{onClick:w,class:"action-icon close-icon"},{default:g(()=>[r(P(J))]),_:1})])]),t("div",{class:"chat-messages",ref_key:"messagesRef",ref:$},[i.value.length===0?(c(),s("div",ct,[...a[4]||(a[4]=[t("div",{class:"empty-icon"},"🤖",-1),t("p",null,"你好，我是飞鱼 AI 助手",-1),t("p",{class:"empty-hint"},"我可以帮你：",-1),t("ul",{class:"empty-list"},[t("li",null,"📝 解答飞鱼 Admin 使用问题"),t("li",null,"💻 提供开发建议和代码示例"),t("li",null,"🔧 辅助进行 NL2SQL 自然语言查询"),t("li",null,"📖 解读系统功能和工作流")],-1)])])):q("",!0),(c(!0),s(T,null,N(i.value,(n,S)=>(c(),s("div",{key:S,class:m(["message",n.role])},[n.role==="ai"?(c(),s("div",st,[...a[5]||(a[5]=[t("svg",{width:"18",height:"18",viewBox:"0 0 48 48",fill:"none"},[t("rect",{width:"48",height:"48",rx:"12",fill:"#2563EB"}),t("path",{d:"M14 24L24 14L34 24L24 34L14 24Z",fill:"#fff"}),t("circle",{cx:"24",cy:"24",r:"5",fill:"#2563EB"})],-1)])])):q("",!0),t("div",ot,[n.role==="ai"?(c(),s("div",it,[n.loading?(c(),s("span",lt,[...a[6]||(a[6]=[t("span",null,null,-1),t("span",null,null,-1),t("span",null,null,-1)])])):(c(),s("span",{key:1,innerHTML:z(n.content)},null,8,rt))])):(c(),s("div",ut,u(n.content),1)),n.role==="ai"?(c(),s("div",vt,[t("span",{class:"copy-btn",onClick:At=>H(n.content)},"复制",8,pt)])):q("",!0)]),n.role==="user"?(c(),s("div",bt,[r(_,null,{default:g(()=>[r(P(Q))]),_:1})])):q("",!0)],2))),128))],512),i.value.length===0?(c(),s("div",mt,[a[7]||(a[7]=t("span",{class:"quick-label"},"试试问我：",-1)),t("div",ht,[(c(),s(T,null,N(v,n=>r(f,{key:n,size:"small",onClick:S=>j(n)},{default:g(()=>[K(u(n),1)]),_:2},1032,["onClick"])),64))])])):q("",!0),t("div",gt,[t("div",qt,[r(k,{modelValue:y.value,"onUpdate:modelValue":a[0]||(a[0]=n=>y.value=n),rows:2,type:"textarea",resize:"none",placeholder:"输入问题，Shift+Enter换行，Enter发送...",onKeydown:[O(p(E,["exact","prevent"]),["enter"]),a[1]||(a[1]=O(p(n=>n.preventDefault(),["shift","exact"]),["enter"]))]},null,8,["modelValue","onKeydown"]),r(f,{type:"primary",class:"send-btn",disabled:!y.value.trim()||h.value,loading:h.value,onClick:E},{default:g(()=>[h.value?q("",!0):(c(),W(_,{key:0},{default:g(()=>[r(P(Y))]),_:1})),h.value?q("",!0):(c(),s("span",wt,"发送"))]),_:1},8,["disabled","loading"])]),a[8]||(a[8]=t("div",{class:"input-hint"},"Enter 发送 · Shift+Enter 换行",-1))])])):q("",!0)]),_:1})])}}},yt=A(ft,[["__scopeId","data-v-59010945"]]),$t={class:"doc-container"},_t={class:"doc-body"},kt={class:"doc-toc"},xt={class:"toc-section"},Tt={class:"doc-main"},Nt={id:"tech-stack",class:"doc-section"},Mt={class:"stack-grid"},Ct={class:"stack-grid"},Lt={id:"features",class:"doc-section"},Pt={class:"features-grid"},Et={class:"feat-icon"},St={id:"crud-example",class:"doc-section"},It={id:"troubleshooting",class:"doc-section"},Ot={class:"qa-list"},Rt={__name:"index",setup(U){const o=x("overview"),y=[{name:"Vue 3.5",desc:"渐进式前端框架，组合式 API",emoji:"⚡",color:"#42b983"},{name:"Vite 5",desc:"下一代前端构建工具，HMR 热更新",emoji:"🚀",color:"#646cff"},{name:"Element Plus 2",desc:"Vue3 UI 组件库，70+ 组件",emoji:"🎨",color:"#409eff"},{name:"Pinia",desc:"新一代状态管理",emoji:"📦",color:"#f7df1e"},{name:"Vue Router 4",desc:"官方路由，支持懒加载",emoji:"🛤️",color:"#4abf8a"},{name:"Axios",desc:"Promise HTTP 客户端，统一拦截",emoji:"🌐",color:"#5a29e4"}],h=[{name:"PHP 8.2",desc:"后端核心语言，JIT 编译",emoji:"🐘",color:"#777bb4"},{name:"ThinkPHP 8",desc:"高性能 PHP 框架",emoji:"⚡",color:"#63b500"},{name:"MySQL 8.0",desc:"关系型数据库，InnoDB 引擎",emoji:"🐬",color:"#00758f"},{name:"Redis",desc:"缓存 + Session + 队列",emoji:"🔴",color:"#dc382d"},{name:"JWT",desc:"无状态认证，令牌自动续期",emoji:"🔑",color:"#d63aff"},{name:"RBAC",desc:"基于角色的访问控制",emoji:"🔐",color:"#f7c948"}],i=[{icon:"👤",title:"用户管理",desc:"用户CRUD、状态管理、个人信息"},{icon:"🔐",title:"角色权限",desc:"RBAC模型，菜单+按钮级权限控制"},{icon:"📋",title:"菜单管理",desc:"动态菜单配置，权限分配"},{icon:"🏢",title:"部门管理",desc:"组织架构树形管理"},{icon:"💼",title:"岗位管理",desc:"岗位序列管理"},{icon:"📝",title:"操作日志",desc:"操作行为记录，可追溯"},{icon:"🔑",title:"登录日志",desc:"登录记录，异常登录告警"},{icon:"⚙️",title:"参数配置",desc:"系统参数动态配置"},{icon:"📖",title:"数据字典",desc:"枚举类型统一管理"},{icon:"📁",title:"文件上传",desc:"本地上传、CDN、OSS"},{icon:"⚡",title:"代码生成器",desc:"一键生成CRUD，可视化配置"},{icon:"🏢",title:"多租户",desc:"数据完全隔离，SaaS支持"},{icon:"🔄",title:"工作流引擎",desc:"可视化流程设计器"},{icon:"📊",title:"数据大屏",desc:"拖拽式大屏设计器"},{icon:"🔔",title:"消息通知",desc:"多渠道通知推送"},{icon:"⏰",title:"定时任务",desc:"Crontab任务调度"},{icon:"📝",title:"表单设计器",desc:"拖拽式表单构建"},{icon:"💳",title:"支付渠道",desc:"微信/支付宝支付"},{icon:"📰",title:"富文本编辑器",desc:"Markdown/WYSIWYG"},{icon:"💻",title:"代码编辑器",desc:"Monaco Editor"}],$=[{q:"页面空白，控制台 401 Unauthorized",a:"Token 过期或未携带。检查 Header 是否正确添加 Authorization: Bearer {token}，或清除 localStorage 重新登录。"},{q:"后端接口返回 502 Bad Gateway",a:"Nginx 无法连接 PHP-FPM。检查 PHP-FPM 是否运行（systemctl status php-fpm），端口是否一致（默认9000）。"},{q:'文件上传失败，提示"上传目录不可写"',a:"执行 chmod -R 777 /www/wwwroot/feiyuadmin/backend/public/uploads，并确保 owner 为 nginx 用户。"},{q:"登录后刷新页面跳转回登录页",a:"通常是 Token 校验失败。检查后端 jwt.secret 配置、前后端密钥是否一致，或 Redis 连接是否正常。"},{q:"定时任务没有执行",a:"确认 Supervisor 进程是否在运行（supervisorctl status），以及 think queue:work 命令路径是否正确。"},{q:"数据库迁移报错 SQLSTATE[42000]",a:"检查 MySQL 版本是否 >= 5.7，确认数据库字符集为 utf8mb4，以及是否有对应权限。"}],v=b=>{const e=document.getElementById(b);e&&(e.scrollIntoView({behavior:"smooth",block:"start"}),o.value=b)};let w;return D(()=>{w=new IntersectionObserver(b=>{b.forEach(e=>{e.isIntersecting&&(o.value=e.target.id)})},{threshold:.2}),document.querySelectorAll(".doc-section[id]").forEach(b=>w.observe(b))}),tt(()=>w==null?void 0:w.disconnect()),(b,e)=>(c(),s("div",$t,[e[24]||(e[24]=M('<header class="doc-header" data-v-b0c5d8ce><div class="header-inner" data-v-b0c5d8ce><a href="/pc/" class="back-home" data-v-b0c5d8ce><svg width="28" height="28" viewBox="0 0 48 48" fill="none" data-v-b0c5d8ce><rect width="48" height="48" rx="12" fill="#2563EB" data-v-b0c5d8ce></rect><path d="M14 24L24 14L34 24L24 34L14 24Z" fill="#fff" data-v-b0c5d8ce></path><circle cx="24" cy="24" r="5" fill="#2563EB" data-v-b0c5d8ce></circle></svg><span data-v-b0c5d8ce>飞鱼 Admin</span></a><nav class="header-nav" data-v-b0c5d8ce><a href="/doc#overview" data-v-b0c5d8ce>概览</a><a href="/doc#quick-start" data-v-b0c5d8ce>快速开始</a><a href="/doc#api-reference" data-v-b0c5d8ce>API</a><a href="/doc#crud-example" data-v-b0c5d8ce>开发指南</a><a href="/doc#deployment" data-v-b0c5d8ce>部署</a></nav><a href="http://demo.fydev.cn/admin" target="_blank" class="btn-primary" data-v-b0c5d8ce>进入后台</a></div></header>',1)),t("div",_t,[t("nav",kt,[t("div",xt,[e[10]||(e[10]=t("div",{class:"toc-title"},"📖 文档",-1)),t("ul",null,[t("li",null,[t("a",{href:"#overview",class:m({active:o.value==="overview"}),onClick:e[0]||(e[0]=p(d=>v("overview"),["prevent"]))},"项目概览",2)]),t("li",null,[t("a",{href:"#tech-stack",class:m({active:o.value==="tech-stack"}),onClick:e[1]||(e[1]=p(d=>v("tech-stack"),["prevent"]))},"技术栈",2)]),t("li",null,[t("a",{href:"#features",class:m({active:o.value==="features"}),onClick:e[2]||(e[2]=p(d=>v("features"),["prevent"]))},"功能特性",2)]),t("li",null,[t("a",{href:"#quick-start",class:m({active:o.value==="quick-start"}),onClick:e[3]||(e[3]=p(d=>v("quick-start"),["prevent"]))},"快速开始",2)]),t("li",null,[t("a",{href:"#crud-example",class:m({active:o.value==="crud-example"}),onClick:e[4]||(e[4]=p(d=>v("crud-example"),["prevent"]))},"CRUD开发示例",2)]),t("li",null,[t("a",{href:"#api-reference",class:m({active:o.value==="api-reference"}),onClick:e[5]||(e[5]=p(d=>v("api-reference"),["prevent"]))},"API接口文档",2)]),t("li",null,[t("a",{href:"#error-codes",class:m({active:o.value==="error-codes"}),onClick:e[6]||(e[6]=p(d=>v("error-codes"),["prevent"]))},"错误码说明",2)]),t("li",null,[t("a",{href:"#deployment",class:m({active:o.value==="deployment"}),onClick:e[7]||(e[7]=p(d=>v("deployment"),["prevent"]))},"生产部署",2)]),t("li",null,[t("a",{href:"#nginx-config",class:m({active:o.value==="nginx-config"}),onClick:e[8]||(e[8]=p(d=>v("nginx-config"),["prevent"]))},"Nginx配置",2)]),t("li",null,[t("a",{href:"#troubleshooting",class:m({active:o.value==="troubleshooting"}),onClick:e[9]||(e[9]=p(d=>v("troubleshooting"),["prevent"]))},"问题排查",2)])])])]),t("main",Tt,[e[21]||(e[21]=M('<section id="overview" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>项目概览</h2><p class="lead" data-v-b0c5d8ce>飞鱼 Admin 是一套基于 <strong data-v-b0c5d8ce>Vue3 + Vite + Element Plus</strong> 前端架构，配合 <strong data-v-b0c5d8ce>ThinkPHP 8</strong> 后端框架的企业级后台管理解决方案。完全免费开源，采用 MIT 许可证，可商用。</p><div class="info-cards" data-v-b0c5d8ce><div class="info-card" data-v-b0c5d8ce><div class="ci blue" data-v-b0c5d8ce>🌐</div><div data-v-b0c5d8ce><h4 data-v-b0c5d8ce>前后端分离</h4><p data-v-b0c5d8ce>标准 RESTful API，支持多端复用</p></div></div><div class="info-card" data-v-b0c5d8ce><div class="ci green" data-v-b0c5d8ce>⚡</div><div data-v-b0c5d8ce><h4 data-v-b0c5d8ce>代码生成器</h4><p data-v-b0c5d8ce>一键生成 CRUD，效率提升 80%</p></div></div><div class="info-card" data-v-b0c5d8ce><div class="ci purple" data-v-b0c5d8ce>🔐</div><div data-v-b0c5d8ce><h4 data-v-b0c5d8ce>RBAC 权限</h4><p data-v-b0c5d8ce>菜单+按钮级细粒度权限控制</p></div></div><div class="info-card" data-v-b0c5d8ce><div class="ci orange" data-v-b0c5d8ce>🏢</div><div data-v-b0c5d8ce><h4 data-v-b0c5d8ce>多租户</h4><p data-v-b0c5d8ce>数据完全隔离，一套系统服务多客户</p></div></div></div><table class="meta-table" data-v-b0c5d8ce><tbody data-v-b0c5d8ce><tr data-v-b0c5d8ce><td class="lbl" data-v-b0c5d8ce>前端框架</td><td data-v-b0c5d8ce>Vue 3.5 + Vite 5 + Element Plus 2</td></tr><tr data-v-b0c5d8ce><td class="lbl" data-v-b0c5d8ce>后端框架</td><td data-v-b0c5d8ce>ThinkPHP 8 + PHP 8.0+</td></tr><tr data-v-b0c5d8ce><td class="lbl" data-v-b0c5d8ce>数据库</td><td data-v-b0c5d8ce>MySQL 5.7+ / 8.0</td></tr><tr data-v-b0c5d8ce><td class="lbl" data-v-b0c5d8ce>认证方式</td><td data-v-b0c5d8ce>JWT Bearer Token</td></tr><tr data-v-b0c5d8ce><td class="lbl" data-v-b0c5d8ce>开源协议</td><td data-v-b0c5d8ce>MIT License</td></tr></tbody></table></section>',1)),t("section",Nt,[e[11]||(e[11]=t("h2",null,"技术栈",-1)),e[12]||(e[12]=t("h3",null,"前端技术",-1)),t("div",Mt,[(c(),s(T,null,N(y,d=>t("div",{class:"stack-item",key:d.name},[t("div",{class:"si",style:R({background:d.color})},u(d.emoji),5),t("div",null,[t("strong",null,u(d.name),1),t("span",null,u(d.desc),1)])])),64))]),e[13]||(e[13]=t("h3",null,"后端技术",-1)),t("div",Ct,[(c(),s(T,null,N(h,d=>t("div",{class:"stack-item",key:d.name},[t("div",{class:"si",style:R({background:d.color})},u(d.emoji),5),t("div",null,[t("strong",null,u(d.name),1),t("span",null,u(d.desc),1)])])),64))])]),t("section",Lt,[e[14]||(e[14]=t("h2",null,"功能特性",-1)),t("div",Pt,[(c(),s(T,null,N(i,d=>t("div",{class:"feat",key:d.title},[t("div",Et,u(d.icon),1),t("h3",null,u(d.title),1),t("p",null,u(d.desc),1)])),64))])]),e[22]||(e[22]=M(`<section id="quick-start" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>快速开始</h2><h3 data-v-b0c5d8ce>环境要求</h3><div class="req-grid" data-v-b0c5d8ce><div class="req" data-v-b0c5d8ce><span class="ri" data-v-b0c5d8ce>🐘</span><div data-v-b0c5d8ce><strong data-v-b0c5d8ce>PHP</strong><p data-v-b0c5d8ce>≥ 8.0，推荐 8.2</p></div></div><div class="req" data-v-b0c5d8ce><span class="ri" data-v-b0c5d8ce>🐬</span><div data-v-b0c5d8ce><strong data-v-b0c5d8ce>MySQL</strong><p data-v-b0c5d8ce>≥ 5.7.36 / 8.0</p></div></div><div class="req" data-v-b0c5d8ce><span class="ri" data-v-b0c5d8ce>📦</span><div data-v-b0c5d8ce><strong data-v-b0c5d8ce>Node.js</strong><p data-v-b0c5d8ce>≥ 16，推荐 20 LTS</p></div></div><div class="req" data-v-b0c5d8ce><span class="ri" data-v-b0c5d8ce>⚡</span><div data-v-b0c5d8ce><strong data-v-b0c5d8ce>pnpm</strong><p data-v-b0c5d8ce>≥ 7.0</p></div></div></div><h3 data-v-b0c5d8ce>前端部署</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># 安装依赖
pnpm install

# 开发模式
pnpm run dev

# PC端构建
pnpm run build
# 输出: ../backend/public/pc/</code></pre><h3 data-v-b0c5d8ce>后端部署</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># 配置数据库
# 编辑 backend/config/database.php

# 访问安装向导
http://your-domain.com/install</code></pre></section>`,1)),t("section",St,[e[15]||(e[15]=M(`<h2 data-v-b0c5d8ce>CRUD 开发示例</h2><p class="lead" data-v-b0c5d8ce>以&quot;新闻管理&quot;模块为例，演示从后端到前端的完整开发流程。</p><div class="step-nav" data-v-b0c5d8ce><span class="step active" data-v-b0c5d8ce>① 数据库</span><span class="step" data-v-b0c5d8ce>② 后端Model</span><span class="step" data-v-b0c5d8ce>③ 后端Controller</span><span class="step" data-v-b0c5d8ce>④ 路由配置</span><span class="step" data-v-b0c5d8ce>⑤ 前端API</span><span class="step" data-v-b0c5d8ce>⑥ 前端页面</span></div><h3 data-v-b0c5d8ce>① 创建数据表</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>CREATE TABLE \`news\` (
  \`id\` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT &#39;ID&#39;,
  \`title\` varchar(200) NOT NULL DEFAULT &#39;&#39; COMMENT &#39;标题&#39;,
  \`category_id\` int unsigned NOT NULL DEFAULT 0 COMMENT &#39;分类ID&#39;,
  \`author\` varchar(50) NOT NULL DEFAULT &#39;&#39; COMMENT &#39;作者&#39;,
  \`cover\` varchar(500) NOT NULL DEFAULT &#39;&#39; COMMENT &#39;封面图&#39;,
  \`content\` longtext COMMENT &#39;内容&#39;,
  \`status\` tinyint NOT NULL DEFAULT 1 COMMENT &#39;状态:0禁用1启用&#39;,
  \`sort\` int NOT NULL DEFAULT 0 COMMENT &#39;排序&#39;,
  \`views\` int NOT NULL DEFAULT 0 COMMENT &#39;浏览量&#39;,
  \`create_time\` int NOT NULL DEFAULT 0 COMMENT &#39;创建时间&#39;,
  \`update_time\` int NOT NULL DEFAULT 0 COMMENT &#39;更新时间&#39;,
  \`delete_time\` int DEFAULT NULL COMMENT &#39;删除时间&#39;,
  PRIMARY KEY (\`id\`),
  KEY \`idx_category\` (\`category_id\`),
  KEY \`idx_status\` (\`status\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=&#39;新闻表&#39;;</code></pre><h3 data-v-b0c5d8ce>② 后端 Model</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>&lt;?php
// backend/app/model/News.php
declare(strict_types=1);

namespace app\\model;

use think\\Model;
use think\\model\\concern\\SoftDelete;

class News extends Model
{
    use SoftDelete;

    protected $name = &#39;news&#39;;
    protected $deleteTime = &#39;delete_time&#39;;
    protected $autoWriteTimestamp = true;
    protected $createTime = &#39;create_time&#39;;
    protected $updateTime = &#39;update_time&#39;;
    protected $type = [
        &#39;category_id&#39; =&gt; &#39;integer&#39;,
        &#39;status&#39;      =&gt; &#39;integer&#39;,
        &#39;views&#39;       =&gt; &#39;integer&#39;,
    ];

    // 关联分类
    public function category(): \\think\\model\\relation\\BelongsTo
    {
        return $this-&gt;belongsTo(Category::class, &#39;category_id&#39;, &#39;id&#39;);
    }
}</code></pre><h3 data-v-b0c5d8ce>③ 后端 Controller</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>&lt;?php
// backend/app/controller/admin/News.php
declare(strict_types=1);

namespace app\\controller\\admin;

use app\\model\\News as NewsModel;
use think\\Request;
use think\\Response;

class News
{
    protected Request $request;
    protected int $userId = 0;

    public function __construct()
    {
        $this-&gt;request = request();
        $this-&gt;userId  = (int) ($this-&gt;request-&gt;userId ?? 0);
    }

    protected function success(mixed $data = [], string $msg = &#39;操作成功&#39;, int $code = 0): Response
    {
        return json([&#39;code&#39; =&gt; $code, &#39;msg&#39; =&gt; $msg, &#39;data&#39; =&gt; $data]);
    }

    protected function error(string $msg = &#39;操作失败&#39;, int $code = 400): Response
    {
        return json([&#39;code&#39; =&gt; $code, &#39;msg&#39; =&gt; $msg, &#39;data&#39; =&gt; []]);
    }

    protected function page(int $total, array $list): Response
    {
        return json([&#39;code&#39; =&gt; 0, &#39;msg&#39; =&gt; &#39;success&#39;, &#39;total&#39; =&gt; $total, &#39;data&#39; =&gt; $list]);
    }

    protected function param(string $name = &#39;&#39;, mixed $default = null): mixed
    {
        return $this-&gt;request-&gt;param($name, $default);
    }

    protected function getPageParam(): array
    {
        $page  = (int) $this-&gt;param(&#39;page&#39;, 1);
        $limit = (int) $this-&gt;param(&#39;limit&#39;, 20);
        $limit = $limit &gt; 100 ? 100 : $limit;
        return [$page, $limit];
    }

    /** 列表 GET /api/news/list */
    public function list(): Response
    {
        [$page, $limit] = $this-&gt;getPageParam();
        $keyword = $this-&gt;param(&#39;keyword&#39;, &#39;&#39;);
        $status  = $this-&gt;param(&#39;status&#39;, &#39;&#39;);
        $cid     = (int) $this-&gt;param(&#39;category_id&#39;, 0);

        $query = NewsModel::with([&#39;category&#39;])-&gt;whereNull(&#39;delete_time&#39;);
        if ($keyword !== &#39;&#39;) {
            $query-&gt;where(function ($q) use ($keyword) {
                $q-&gt;whereLike(&#39;title&#39;, &quot;%{$keyword}%&quot;)
                  -&gt;whereOr(&#39;author&#39;, &#39;like&#39;, &quot;%{$keyword}%&quot;);
            });
        }
        if ($cid &gt; 0) $query-&gt;where(&#39;category_id&#39;, $cid);
        if ($status !== &#39;&#39;) $query-&gt;where(&#39;status&#39;, (int) $status);

        $total = $query-&gt;count();
        $list  = $query-&gt;page($page, $limit)-&gt;order(&#39;id&#39;, &#39;desc&#39;)-&gt;select()-&gt;toArray();
        return $this-&gt;page($total, $list);
    }

    /** 详情 GET /api/news/read?id=1 */
    public function read(): Response
    {
        $id = (int) $this-&gt;param(&#39;id&#39;, 0);
        if (!$id) return $this-&gt;error(&#39;参数错误&#39;);
        $news = NewsModel::with([&#39;category&#39;])-&gt;find($id);
        if (!$news) return $this-&gt;error(&#39;记录不存在&#39;);
        return $this-&gt;success($news);
    }

    /** 新增 POST /api/news/save */
    public function save(): Response
    {
        $title = $this-&gt;param(&#39;title&#39;, &#39;&#39;);
        if (empty($title)) return $this-&gt;error(&#39;标题不能为空&#39;);
        $news = new NewsModel();
        $news-&gt;title       = $title;
        $news-&gt;category_id = (int) $this-&gt;param(&#39;category_id&#39;, 0);
        $news-&gt;author      = $this-&gt;param(&#39;author&#39;, &#39;&#39;);
        $news-&gt;cover       = $this-&gt;param(&#39;cover&#39;, &#39;&#39;);
        $news-&gt;content     = $this-&gt;param(&#39;content&#39;, &#39;&#39;);
        $news-&gt;status      = (int) $this-&gt;param(&#39;status&#39;, 1);
        $news-&gt;sort        = (int) $this-&gt;param(&#39;sort&#39;, 0);
        $news-&gt;save();
        return $this-&gt;success([&#39;id&#39; =&gt; $news-&gt;id], &#39;新增成功&#39;);
    }

    /** 更新 POST /api/news/update */
    public function update(): Response
    {
        $id = (int) $this-&gt;param(&#39;id&#39;, 0);
        if (!$id) return $this-&gt;error(&#39;参数错误&#39;);
        $news = NewsModel::find($id);
        if (!$news) return $this-&gt;error(&#39;记录不存在&#39;);
        $title = $this-&gt;param(&#39;title&#39;, &#39;&#39;);
        if ($title !== &#39;&#39;) $news-&gt;title = $title;
        $news-&gt;category_id = (int) $this-&gt;param(&#39;category_id&#39;, $news-&gt;category_id);
        if ($this-&gt;param(&#39;author&#39;, &#39;&#39;)   !== &#39;&#39;) $news-&gt;author  = $this-&gt;param(&#39;author&#39;);
        if ($this-&gt;param(&#39;status&#39;, &#39;&#39;)   !== &#39;&#39;) $news-&gt;status  = (int) $this-&gt;param(&#39;status&#39;);
        if ($this-&gt;param(&#39;sort&#39;, &#39;&#39;)     !== &#39;&#39;) $news-&gt;sort    = (int) $this-&gt;param(&#39;sort&#39;);
        $news-&gt;save();
        return $this-&gt;success([], &#39;更新成功&#39;);
    }

    /** 删除 POST /api/news/delete */
    public function delete(): Response
    {
        $id = (int) $this-&gt;param(&#39;id&#39;, 0);
        if (!$id) return $this-&gt;error(&#39;参数错误&#39;);
        $news = NewsModel::find($id);
        if (!$news) return $this-&gt;error(&#39;记录不存在&#39;);
        $news-&gt;delete();
        return $this-&gt;success([], &#39;删除成功&#39;);
    }
}</code></pre><h3 data-v-b0c5d8ce>④ 后端路由</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># backend/route/api.php
use think\\facade\\Route;

Route::group(&#39;news&#39;, function () {
    Route::get(&#39;list&#39;,   &#39;admin.News/list&#39;);
    Route::get(&#39;read&#39;,   &#39;admin.News/read&#39;);
    Route::post(&#39;save&#39;,  &#39;admin.News/save&#39;);
    Route::post(&#39;update&#39;,&#39;admin.News/update&#39;);
    Route::post(&#39;delete&#39;,&#39;admin.News/delete&#39;);
})-&gt;middleware(&#39;adminAuth&#39;);</code></pre><h3 data-v-b0c5d8ce>⑤ 前端 API 文件</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>// frontend/src/api/news.js
import request from &#39;@/utils/request&#39;

export const getNewsList = (params) =&gt;
  request({ url: &#39;/news/list&#39;, method: &#39;get&#39;, params })

export const getNews = (id) =&gt;
  request({ url: &#39;/news/read&#39;, method: &#39;get&#39;, params: { id } })

export const createNews = (data) =&gt;
  request({ url: &#39;/news/save&#39;, method: &#39;post&#39;, data })

export const updateNews = (data) =&gt;
  request({ url: &#39;/news/update&#39;, method: &#39;post&#39;, data })

export const deleteNews = (id) =&gt;
  request({ url: &#39;/news/delete&#39;, method: &#39;post&#39;, data: { id } })</code></pre><h3 data-v-b0c5d8ce>⑥ 前端页面（Vue3）</h3>`,14)),t("pre",null,[t("code",null,`<template>
  <div class="news-page">
    <div class="search-bar">
      <el-input v-model="query.keyword" placeholder="搜索标题/作者" clearable @keyup.enter="fetchList" />
      <el-select v-model="query.status" placeholder="状态" clearable>
        <el-option label="启用" :value="1" />
        <el-option label="禁用" :value="0" />
      </el-select>
      <el-button type="primary" @click="fetchList">查询</el-button>
      <el-button type="primary" @click="openDialog('create')">新增新闻</el-button>
    </div>

    <el-table :data="list" v-loading="loading" stripe>
      <el-table-column prop="id" label="ID" width="80" />
      <el-table-column prop="title" label="标题" min-width="200" />
      <el-table-column prop="category.name" label="分类" width="120" />
      <el-table-column prop="author" label="作者" width="120" />
      <el-table-column prop="status" label="状态" width="100">
        <template #default="{ row }">
          <el-tag :type="row.status === 1 ? 'success' : 'danger'">
            `+u(b.row.status===1?"启用":"禁用")+`
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="200" fixed="right">
        <template #default="{ row }">
          <el-button type="primary" link @click="openDialog('edit', row)">编辑</el-button>
          <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-model:current-page="query.page"
      v-model:page-size="query.limit"
      :total="total"
      :page-sizes="[10, 20, 50]"
      layout="total, sizes, prev, pager, next"
      @change="fetchList"
      style="margin-top: 16px"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { getNewsList, deleteNews } from '@/api/news'

const loading = ref(false)
const list = ref([])
const total = ref(0)
const query = reactive({ page: 1, limit: 20, keyword: '', status: '' })

const fetchList = async () => {
  loading.value = true
  try {
    const res = await getNewsList(query)
    list.value = res.data
    total.value = res.total
  } finally {
    loading.value = false
  }
}

const handleDelete = async (id) => {
  await ElMessageBox.confirm('确定删除该新闻？', '提示', { type: 'warning' })
  await deleteNews(id)
  ElMessage.success('删除成功')
  fetchList()
}

onMounted(() => fetchList())
<\/script>`,1)])]),e[23]||(e[23]=M(`<section id="api-reference" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>API 接口文档</h2><h3 data-v-b0c5d8ce>统一响应格式</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>{
  &quot;code&quot;: 0,           // 0=成功，其他=失败（见错误码表）
  &quot;msg&quot;: &quot;success&quot;,   // 提示信息
  &quot;total&quot;: 100,       // 分页时返回总数
  &quot;data&quot;: {}           // 返回数据
}</code></pre><h3 data-v-b0c5d8ce>认证 Header</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json</code></pre><h3 data-v-b0c5d8ce>分页参数</h3><div class="table-wrap" data-v-b0c5d8ce><table class="param-tbl" data-v-b0c5d8ce><thead data-v-b0c5d8ce><tr data-v-b0c5d8ce><th data-v-b0c5d8ce>参数名</th><th data-v-b0c5d8ce>类型</th><th data-v-b0c5d8ce>必填</th><th data-v-b0c5d8ce>说明</th></tr></thead><tbody data-v-b0c5d8ce><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>page</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>页码，默认 1</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>limit</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>每页条数，默认 20，最大 100</td></tr></tbody></table></div><h3 data-v-b0c5d8ce>登录认证 /login</h3><div class="api-grp" data-v-b0c5d8ce><span class="method post" data-v-b0c5d8ce>POST</span><span class="path" data-v-b0c5d8ce>/api/login — 用户登录</span></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>// 请求
POST /api/login
{ &quot;username&quot;: &quot;admin&quot;, &quot;password&quot;: &quot;admin123&quot; }

// 成功响应
{
  &quot;code&quot;: 0,
  &quot;msg&quot;: &quot;登录成功&quot;,
  &quot;data&quot;: {
    &quot;token&quot;: &quot;eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...&quot;,
    &quot;expires_in&quot;: 86400,
    &quot;user_info&quot;: { &quot;id&quot;: 1, &quot;username&quot;: &quot;admin&quot;, &quot;nickname&quot;: &quot;超级管理员&quot; }
  }
}</code></pre><h3 data-v-b0c5d8ce>用户管理 /user</h3><div class="api-grp" data-v-b0c5d8ce><span class="method get" data-v-b0c5d8ce>GET</span><span class="path" data-v-b0c5d8ce>/api/user/list — 用户列表</span></div><div class="table-wrap" data-v-b0c5d8ce><table class="param-tbl" data-v-b0c5d8ce><thead data-v-b0c5d8ce><tr data-v-b0c5d8ce><th data-v-b0c5d8ce>参数名</th><th data-v-b0c5d8ce>类型</th><th data-v-b0c5d8ce>必填</th><th data-v-b0c5d8ce>说明</th></tr></thead><tbody data-v-b0c5d8ce><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>keyword</td><td data-v-b0c5d8ce>string</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>搜索：用户名/昵称/手机号</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>dept_id</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>部门ID（包含子部门）</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>status</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>状态：0=禁用，1=启用</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>page</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>页码，默认1</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>limit</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>每页条数，默认20</td></tr></tbody></table></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>{
  &quot;code&quot;: 0,
  &quot;msg&quot;: &quot;success&quot;,
  &quot;total&quot;: 58,
  &quot;data&quot;: [
    {
      &quot;id&quot;: 1,
      &quot;username&quot;: &quot;admin&quot;,
      &quot;nickname&quot;: &quot;超级管理员&quot;,
      &quot;mobile&quot;: &quot;13800138000&quot;,
      &quot;dept&quot;: { &quot;id&quot;: 1, &quot;name&quot;: &quot;技术部&quot; },
      &quot;post&quot;: { &quot;id&quot;: 1, &quot;name&quot;: &quot;技术总监&quot; },
      &quot;roles&quot;: [&quot;super_admin&quot;],
      &quot;status&quot;: 1,
      &quot;create_time&quot;: &quot;2024-01-01 08:00:00&quot;
    }
  ]
}</code></pre><div class="api-grp" data-v-b0c5d8ce><span class="method post" data-v-b0c5d8ce>POST</span><span class="path" data-v-b0c5d8ce>/api/user/save — 新增用户</span></div><div class="table-wrap" data-v-b0c5d8ce><table class="param-tbl" data-v-b0c5d8ce><thead data-v-b0c5d8ce><tr data-v-b0c5d8ce><th data-v-b0c5d8ce>参数名</th><th data-v-b0c5d8ce>类型</th><th data-v-b0c5d8ce>必填</th><th data-v-b0c5d8ce>说明</th></tr></thead><tbody data-v-b0c5d8ce><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>username</td><td data-v-b0c5d8ce>string</td><td data-v-b0c5d8ce>✅</td><td data-v-b0c5d8ce>用户名，4-20位</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>password</td><td data-v-b0c5d8ce>string</td><td data-v-b0c5d8ce>✅</td><td data-v-b0c5d8ce>密码，6-20位</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>nickname</td><td data-v-b0c5d8ce>string</td><td data-v-b0c5d8ce>✅</td><td data-v-b0c5d8ce>昵称</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>role_ids</td><td data-v-b0c5d8ce>array</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>角色ID数组，如 [1, 2]</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>mobile</td><td data-v-b0c5d8ce>string</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>手机号</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>dept_id</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>部门ID</td></tr><tr data-v-b0c5d8ce><td data-v-b0c5d8ce>status</td><td data-v-b0c5d8ce>int</td><td data-v-b0c5d8ce>否</td><td data-v-b0c5d8ce>状态，默认1</td></tr></tbody></table></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>// 请求示例
POST /api/user/save
{
  &quot;username&quot;: &quot;zhangsan&quot;,
  &quot;password&quot;: &quot;123456&quot;,
  &quot;nickname&quot;: &quot;张三&quot;,
  &quot;role_ids&quot;: [2, 3],
  &quot;mobile&quot;: &quot;13900139000&quot;
}

// 成功响应
{ &quot;code&quot;: 0, &quot;msg&quot;: &quot;新增成功&quot;, &quot;data&quot;: { &quot;id&quot;: 10 } }</code></pre><div class="api-grp" data-v-b0c5d8ce><span class="method post" data-v-b0c5d8ce>POST</span><span class="path" data-v-b0c5d8ce>/api/user/update — 更新用户</span></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>// 请求示例（只需传需要更新的字段，id必传）
POST /api/user/update
{ &quot;id&quot;: 10, &quot;nickname&quot;: &quot;张三（新）&quot;, &quot;role_ids&quot;: [2], &quot;status&quot;: 1 }</code></pre><div class="api-grp" data-v-b0c5d8ce><span class="method post" data-v-b0c5d8ce>POST</span><span class="path" data-v-b0c5d8ce>/api/user/delete — 删除用户</span></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>// 请求
POST /api/user/delete
{ &quot;id&quot;: 10 }

// 成功: { &quot;code&quot;: 0, &quot;msg&quot;: &quot;删除成功&quot;, &quot;data&quot;: [] }
// 失败: { &quot;code&quot;: 400, &quot;msg&quot;: &quot;不能删除当前登录用户&quot;, &quot;data&quot;: [] }</code></pre><h3 data-v-b0c5d8ce>角色管理 /role</h3><div class="api-grp" data-v-b0c5d8ce><span class="method get" data-v-b0c5d8ce>GET</span><span class="path" data-v-b0c5d8ce>/api/role/list — 角色列表</span></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>{
  &quot;code&quot;: 0,
  &quot;msg&quot;: &quot;success&quot;,
  &quot;total&quot;: 5,
  &quot;data&quot;: [
    { &quot;id&quot;: 1, &quot;name&quot;: &quot;超级管理员&quot;, &quot;code&quot;: &quot;super_admin&quot;, &quot;status&quot;: 1, &quot;sort&quot;: 0 },
    { &quot;id&quot;: 2, &quot;name&quot;: &quot;运营主管&quot;,   &quot;code&quot;: &quot;operation_manager&quot;, &quot;status&quot;: 1, &quot;sort&quot;: 1 }
  ]
}</code></pre><div class="api-grp" data-v-b0c5d8ce><span class="method post" data-v-b0c5d8ce>POST</span><span class="path" data-v-b0c5d8ce>/api/role/save — 新增角色</span></div><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>POST /api/role/save
{
  &quot;name&quot;: &quot;内容编辑&quot;,
  &quot;code&quot;: &quot;content_editor&quot;,
  &quot;status&quot;: 1,
  &quot;sort&quot;: 10,
  &quot;remark&quot;: &quot;内容模块编辑权限&quot;
}</code></pre></section><section id="error-codes" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>错误码说明</h2><div class="table-wrap" data-v-b0c5d8ce><table class="param-tbl" data-v-b0c5d8ce><thead data-v-b0c5d8ce><tr data-v-b0c5d8ce><th data-v-b0c5d8ce>错误码</th><th data-v-b0c5d8ce>说明</th><th data-v-b0c5d8ce>处理建议</th></tr></thead><tbody data-v-b0c5d8ce><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>0</td><td data-v-b0c5d8ce>成功</td><td data-v-b0c5d8ce>—</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>400</td><td data-v-b0c5d8ce>业务逻辑错误</td><td data-v-b0c5d8ce>如&quot;用户名已存在&quot;，根据 msg 提示处理</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>401</td><td data-v-b0c5d8ce>未授权 / Token失效</td><td data-v-b0c5d8ce>跳转登录页，重新获取 Token</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>403</td><td data-v-b0c5d8ce>无权限访问</td><td data-v-b0c5d8ce>当前账号无此接口权限，联系管理员</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>404</td><td data-v-b0c5d8ce>接口不存在</td><td data-v-b0c5d8ce>检查请求路径是否正确</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>422</td><td data-v-b0c5d8ce>参数校验失败</td><td data-v-b0c5d8ce>检查必填参数是否完整</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>500</td><td data-v-b0c5d8ce>服务器内部错误</td><td data-v-b0c5d8ce>查看后端日志，联系技术支持</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>502</td><td data-v-b0c5d8ce>网关错误</td><td data-v-b0c5d8ce>Nginx 配置问题或后端服务未启动</td></tr><tr data-v-b0c5d8ce><td class="code" data-v-b0c5d8ce>503</td><td data-v-b0c5d8ce>服务不可用</td><td data-v-b0c5d8ce>后端进程崩溃或内存不足</td></tr></tbody></table></div><div class="callout info" data-v-b0c5d8ce><strong data-v-b0c5d8ce>前端 HTTP 状态码处理：</strong>401 自动跳转登录，404 提示&quot;接口不存在&quot;，500+ 提示&quot;服务器错误&quot;。具体业务错误码在响应 body 的 <code data-v-b0c5d8ce>code</code> 字段中。 </div></section><section id="deployment" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>生产环境部署</h2><h3 data-v-b0c5d8ce>基础环境安装（CentOS）</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># 安装 Nginx
yum install -y nginx
systemctl enable --now nginx

# 安装 PHP 8.2
dnf module enable php:8.2 -y
yum install -y php-fpm php-cli php-mysqlnd php-json php-mbstring php-xml php-curl php-gd php-bcmath php-pdo

# 安装 MySQL 8.0
yum install -y mysql-community-server
systemctl enable --now mysqld

# 安装 Redis
yum install -y redis
systemctl enable --now redis</code></pre><h3 data-v-b0c5d8ce>项目部署步骤</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># 1. 部署后端代码到 /www/wwwroot/feiyuadmin/backend/
# 2. 前端 PC 构建到 /www/wwwroot/feiyuadmin/backend/public/pc/

# 3. 安装后端依赖
cd /www/wwwroot/feiyuadmin/backend
composer install --no-dev --optimize-autoloader

# 4. 配置 .env
cp .env.example .env
# 编辑 .env 配置数据库连接

# 5. 生成密钥
php think jwt:generate

# 6. 设置权限
chown -R nginx:nginx /www/wwwroot/feiyuadmin/backend
chmod -R 755 /www/wwwroot/feiyuadmin/backend
chmod -R 777 /www/wwwroot/feiyuadmin/backend/runtime
chmod -R 777 /www/wwwroot/feiyuadmin/backend/public/uploads</code></pre><h3 data-v-b0c5d8ce>PHP-FPM 配置</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># /etc/php-fpm.d/www.conf 关键配置
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500  # 防止内存泄漏</code></pre><h3 data-v-b0c5d8ce>Supervisor 守护进程</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># 安装 Supervisor
pip install supervisor

# 添加队列 worker 配置
cat &gt;&gt; /etc/supervisord.d/feiyuadmin.ini &lt;&lt; &#39;EOF&#39;
[program:feiyuadmin-queue]
command=php /www/wwwroot/feiyuadmin/backend/think queue:work --queue default --sleep 3 --tries 3
directory=/www/wwwroot/feiyuadmin/backend
autostart=true
autorestart=true
user=nginx
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log
EOF

supervisorctl reread
supervisorctl update
supervisorctl start feiyuadmin-queue</code></pre></section><section id="nginx-config" class="doc-section" data-v-b0c5d8ce><h2 data-v-b0c5d8ce>Nginx 配置</h2><pre data-v-b0c5d8ce><code data-v-b0c5d8ce># /etc/nginx/conf.d/feiyuadmin.conf

server {
    listen 80;
    server_name feiyuadmin.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name feiyuadmin.example.com;

    ssl_certificate     /etc/nginx/ssl/example.com.pem;
    ssl_certificate_key /etc/nginx/ssl/example.com.key;
    ssl_protocols       TLSv1.2 TLSv1.3;

    root /www/wwwroot/feiyuadmin/backend/public;
    index index.php index.html;

    # Gzip压缩
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

    # 安全头
    add_header X-Frame-Options &quot;SAMEORIGIN&quot; always;
    add_header X-Content-Type-Options &quot;nosniff&quot; always;

    # ========== PC端演示站（静态） ==========
    location /pc/ {
        alias /www/wwwroot/feiyuadmin/backend/public/pc/;
        try_files $uri $uri/ /pc/index.html;
        expires 30d;
        add_header Cache-Control &quot;public, immutable&quot;;
    }

    # ========== 后端 API ==========
    location /api/ {
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size 50m;
    }

    # ========== PHP-FPM ==========
    location ~ \\.php(/|$) {
        proxy_pass http://127.0.0.1:9000;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        client_max_body_size 50m;
    }

    # ========== 禁止访问敏感文件 ==========
    location ~ /\\.env { deny all; }
    location ~ /\\.git  { deny all; }
    location ~ /runtime { deny all; }

    # ========== 静态资源缓存 ==========
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control &quot;public, immutable&quot;;
    }
}</code></pre><h3 data-v-b0c5d8ce>常用命令</h3><pre data-v-b0c5d8ce><code data-v-b0c5d8ce>nginx -t        # 测试配置
nginx -s reload  # 重载配置（不中断）
systemctl restart nginx  # 重启</code></pre></section>`,4)),t("section",It,[e[16]||(e[16]=t("h2",null,"常见问题排查",-1)),t("div",Ot,[(c(),s(T,null,N($,d=>t("div",{class:"qa",key:d.q},[t("h4",null,"❓ "+u(d.q),1),t("p",null,u(d.a),1)])),64))]),e[17]||(e[17]=t("h3",null,"日志查看",-1)),e[18]||(e[18]=t("pre",null,[t("code",null,`# Nginx 错误日志
tail -f /var/log/nginx/feiyuadmin_error.log

# PHP-FPM 错误日志
tail -f /var/log/php-fpm/error.log

# ThinkPHP 应用日志
tail -f /www/wwwroot/feiyuadmin/backend/runtime/log/$(date +%Y%m%d).log

# Supervisor 日志（队列）
tail -f /www/wwwroot/feiyuadmin/backend/runtime/logs/queue.log`)],-1)),e[19]||(e[19]=t("h3",null,"性能调优参考值",-1)),e[20]||(e[20]=t("pre",null,[t("code",null,`# MySQL (my.cnf)
innodb_buffer_pool_size = 1G    # 物理内存的50-70%
max_connections = 500
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Redis (redis.conf)
appendonly yes
appendfsync everysec
maxmemory 512mb
maxmemory-policy allkeys-lru`)],-1))])])]),r(yt)]))}},Ut=A(Rt,[["__scopeId","data-v-b0c5d8ce"]]);export{Ut as default};
