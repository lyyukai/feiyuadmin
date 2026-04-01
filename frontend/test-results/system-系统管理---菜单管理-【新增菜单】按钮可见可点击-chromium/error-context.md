# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: system.spec.js >> 系统管理 - 菜单管理 >> 【新增菜单】按钮可见可点击
- Location: tests/system.spec.js:211:3

# Error details

```
TimeoutError: page.waitForURL: Timeout 10000ms exceeded.
=========================== logs ===========================
waiting for navigation to "**/index**" until "load"
  navigated to "http://39.105.173.6:8088/admin/login"
  navigated to "http://39.105.173.6:8088/admin/dashboard"
============================================================
```

# Page snapshot

```yaml
- generic [ref=e3]:
  - complementary [ref=e4]:
    - generic [ref=e5]:
      - img [ref=e7]
      - generic [ref=e11]: 飞羽后台
    - menubar [ref=e15]:
      - menuitem "系统管理" [expanded] [ref=e16]:
        - generic [ref=e17] [cursor=pointer]:
          - img [ref=e19]
          - generic [ref=e21]: 系统管理
          - img [ref=e23]
        - menu [ref=e25]:
          - menuitem "用户管理" [ref=e26] [cursor=pointer]:
            - img [ref=e28]
            - generic [ref=e30]: 用户管理
          - menuitem "角色管理" [ref=e31] [cursor=pointer]:
            - img [ref=e33]
            - generic [ref=e35]: 角色管理
          - menuitem "菜单管理" [ref=e36] [cursor=pointer]:
            - img [ref=e38]
            - generic [ref=e40]: 菜单管理
          - menuitem "部门管理" [ref=e41] [cursor=pointer]:
            - img [ref=e43]
            - generic [ref=e47]: 部门管理
          - menuitem "岗位管理" [ref=e48] [cursor=pointer]:
            - img [ref=e50]
            - generic [ref=e52]: 岗位管理
      - menuitem "系统配置" [ref=e53]:
        - generic [ref=e54] [cursor=pointer]:
          - img [ref=e56]
          - generic [ref=e58]: 系统配置
          - img [ref=e60]
      - menuitem "日志管理" [ref=e62]:
        - generic [ref=e63] [cursor=pointer]:
          - img [ref=e65]
          - generic [ref=e67]: 日志管理
          - img [ref=e69]
      - menuitem "扩展功能" [ref=e71]:
        - generic [ref=e72] [cursor=pointer]:
          - img [ref=e74]
          - generic [ref=e76]: 扩展功能
          - img [ref=e78]
      - menuitem "渠道管理" [ref=e80]:
        - generic [ref=e81] [cursor=pointer]:
          - img [ref=e83]
          - generic [ref=e86]: 渠道管理
          - img [ref=e88]
      - menuitem "支付管理" [ref=e90]:
        - generic [ref=e91] [cursor=pointer]:
          - img [ref=e93]
          - generic [ref=e96]: 支付管理
          - img [ref=e98]
      - menuitem "系统工具" [ref=e100]:
        - generic [ref=e101] [cursor=pointer]:
          - img [ref=e103]
          - generic [ref=e105]: 系统工具
          - img [ref=e107]
      - menuitem "工作流" [ref=e109]:
        - generic [ref=e110] [cursor=pointer]:
          - img [ref=e112]
          - generic [ref=e115]: 工作流
          - img [ref=e117]
  - generic [ref=e119]:
    - banner [ref=e120]:
      - generic [ref=e121]:
        - button [ref=e122] [cursor=pointer]:
          - img [ref=e124]
        - generic [ref=e126]: 系统管理/菜单管理
      - generic [ref=e129]:
        - img [ref=e132]
        - textbox "搜索菜单..." [ref=e134]
      - generic [ref=e135]:
        - generic "消息通知" [ref=e136] [cursor=pointer]:
          - img [ref=e139]
        - generic "全屏" [ref=e143] [cursor=pointer]:
          - img [ref=e145]
        - generic "布局配置" [ref=e147] [cursor=pointer]:
          - img [ref=e149]
        - button [ref=e152]:
          - img [ref=e153]
        - button "管理员 管理员" [ref=e156]:
          - generic [ref=e157]: 管理员
          - text: 管理员
          - img [ref=e159]
    - generic [ref=e162]:
      - generic [ref=e164] [cursor=pointer]: 工作台
      - generic [ref=e165] [cursor=pointer]:
        - generic [ref=e166]: 菜单管理
        - img [ref=e168]
    - main [ref=e170]:
      - generic [ref=e171]:
        - generic [ref=e172]:
          - button "新增菜单" [ref=e174] [cursor=pointer]:
            - generic [ref=e175]:
              - img [ref=e177]
              - text: 新增菜单
          - generic [ref=e179]:
            - textbox "菜单名称/路由" [ref=e182]
            - button "搜索" [ref=e183] [cursor=pointer]:
              - generic [ref=e184]:
                - img [ref=e186]
                - text: 搜索
            - button "重置" [ref=e188] [cursor=pointer]:
              - generic [ref=e189]:
                - img [ref=e191]
                - text: 重置
        - generic [ref=e193]:
          - generic [ref=e195]: 菜单列表
          - tree [ref=e196]:
            - treeitem "系统管理 - 新增 编辑 删除" [expanded] [ref=e197]:
              - generic [ref=e198] [cursor=pointer]:
                - img [ref=e200]
                - generic [ref=e202]:
                  - generic [ref=e203]:
                    - img [ref=e205]
                    - generic [ref=e207]: 系统管理
                  - generic [ref=e208]: "-"
                  - generic [ref=e209]:
                    - button "新增" [ref=e210]:
                      - generic [ref=e211]: 新增
                    - button "编辑" [ref=e212]:
                      - generic [ref=e213]: 编辑
                    - button "删除" [ref=e214]:
                      - generic [ref=e215]: 删除
              - group [ref=e216]:
                - treeitem "用户管理 /system/user 新增 编辑 删除" [expanded] [ref=e217]:
                  - generic [ref=e219] [cursor=pointer]:
                    - generic [ref=e220]:
                      - img [ref=e222]
                      - generic [ref=e224]: 用户管理
                    - generic [ref=e225]: /system/user
                    - generic [ref=e226]:
                      - button "新增" [ref=e227]:
                        - generic [ref=e228]: 新增
                      - button "编辑" [ref=e229]:
                        - generic [ref=e230]: 编辑
                      - button "删除" [ref=e231]:
                        - generic [ref=e232]: 删除
                  - group [ref=e233]
                - treeitem "角色管理 /system/role 新增 编辑 删除" [expanded] [ref=e234]:
                  - generic [ref=e236] [cursor=pointer]:
                    - generic [ref=e237]:
                      - img [ref=e239]
                      - generic [ref=e241]: 角色管理
                    - generic [ref=e242]: /system/role
                    - generic [ref=e243]:
                      - button "新增" [ref=e244]:
                        - generic [ref=e245]: 新增
                      - button "编辑" [ref=e246]:
                        - generic [ref=e247]: 编辑
                      - button "删除" [ref=e248]:
                        - generic [ref=e249]: 删除
                  - group [ref=e250]
                - treeitem "菜单管理 /system/menu 新增 编辑 删除" [expanded] [ref=e251]:
                  - generic [ref=e253] [cursor=pointer]:
                    - generic [ref=e254]:
                      - img [ref=e256]
                      - generic [ref=e258]: 菜单管理
                    - generic [ref=e259]: /system/menu
                    - generic [ref=e260]:
                      - button "新增" [ref=e261]:
                        - generic [ref=e262]: 新增
                      - button "编辑" [ref=e263]:
                        - generic [ref=e264]: 编辑
                      - button "删除" [ref=e265]:
                        - generic [ref=e266]: 删除
                  - group [ref=e267]
                - treeitem "部门管理 /system/dept 新增 编辑 删除" [expanded] [ref=e268]:
                  - generic [ref=e270] [cursor=pointer]:
                    - generic [ref=e271]:
                      - img [ref=e273]
                      - generic [ref=e277]: 部门管理
                    - generic [ref=e278]: /system/dept
                    - generic [ref=e279]:
                      - button "新增" [ref=e280]:
                        - generic [ref=e281]: 新增
                      - button "编辑" [ref=e282]:
                        - generic [ref=e283]: 编辑
                      - button "删除" [ref=e284]:
                        - generic [ref=e285]: 删除
                  - group [ref=e286]
                - treeitem "岗位管理 /system/post 新增 编辑 删除" [expanded] [ref=e287]:
                  - generic [ref=e289] [cursor=pointer]:
                    - generic [ref=e290]:
                      - img [ref=e292]
                      - generic [ref=e294]: 岗位管理
                    - generic [ref=e295]: /system/post
                    - generic [ref=e296]:
                      - button "新增" [ref=e297]:
                        - generic [ref=e298]: 新增
                      - button "编辑" [ref=e299]:
                        - generic [ref=e300]: 编辑
                      - button "删除" [ref=e301]:
                        - generic [ref=e302]: 删除
                  - group [ref=e303]
            - treeitem "系统配置 - 新增 编辑 删除" [expanded] [ref=e304]:
              - generic [ref=e305] [cursor=pointer]:
                - img [ref=e307]
                - generic [ref=e309]:
                  - generic [ref=e310]:
                    - img [ref=e312]
                    - generic [ref=e314]: 系统配置
                  - generic [ref=e315]: "-"
                  - generic [ref=e316]:
                    - button "新增" [ref=e317]:
                      - generic [ref=e318]: 新增
                    - button "编辑" [ref=e319]:
                      - generic [ref=e320]: 编辑
                    - button "删除" [ref=e321]:
                      - generic [ref=e322]: 删除
              - group [ref=e323]:
                - treeitem "系统配置 /system/config 新增 编辑 删除" [expanded] [ref=e324]:
                  - generic [ref=e326] [cursor=pointer]:
                    - generic [ref=e327]:
                      - img [ref=e329]
                      - generic [ref=e331]: 系统配置
                    - generic [ref=e332]: /system/config
                    - generic [ref=e333]:
                      - button "新增" [ref=e334]:
                        - generic [ref=e335]: 新增
                      - button "编辑" [ref=e336]:
                        - generic [ref=e337]: 编辑
                      - button "删除" [ref=e338]:
                        - generic [ref=e339]: 删除
                  - group [ref=e340]
                - treeitem "字典管理 /system/dict 新增 编辑 删除" [expanded] [ref=e341]:
                  - generic [ref=e343] [cursor=pointer]:
                    - generic [ref=e344]:
                      - img [ref=e346]
                      - generic [ref=e349]: 字典管理
                    - generic [ref=e350]: /system/dict
                    - generic [ref=e351]:
                      - button "新增" [ref=e352]:
                        - generic [ref=e353]: 新增
                      - button "编辑" [ref=e354]:
                        - generic [ref=e355]: 编辑
                      - button "删除" [ref=e356]:
                        - generic [ref=e357]: 删除
                  - group [ref=e358]
            - treeitem "日志管理 - 新增 编辑 删除" [expanded] [ref=e359]:
              - generic [ref=e360] [cursor=pointer]:
                - img [ref=e362]
                - generic [ref=e364]:
                  - generic [ref=e365]:
                    - img [ref=e367]
                    - generic [ref=e369]: 日志管理
                  - generic [ref=e370]: "-"
                  - generic [ref=e371]:
                    - button "新增" [ref=e372]:
                      - generic [ref=e373]: 新增
                    - button "编辑" [ref=e374]:
                      - generic [ref=e375]: 编辑
                    - button "删除" [ref=e376]:
                      - generic [ref=e377]: 删除
              - group [ref=e378]:
                - treeitem "操作日志 /system/log 新增 编辑 删除" [expanded] [ref=e379]:
                  - generic [ref=e381] [cursor=pointer]:
                    - generic [ref=e382]:
                      - img [ref=e384]
                      - generic [ref=e386]: 操作日志
                    - generic [ref=e387]: /system/log
                    - generic [ref=e388]:
                      - button "新增" [ref=e389]:
                        - generic [ref=e390]: 新增
                      - button "编辑" [ref=e391]:
                        - generic [ref=e392]: 编辑
                      - button "删除" [ref=e393]:
                        - generic [ref=e394]: 删除
                  - group [ref=e395]
                - treeitem "登录日志 /system/login_log 新增 编辑 删除" [expanded] [ref=e396]:
                  - generic [ref=e398] [cursor=pointer]:
                    - generic [ref=e399]:
                      - img [ref=e401]
                      - generic [ref=e403]: 登录日志
                    - generic [ref=e404]: /system/login_log
                    - generic [ref=e405]:
                      - button "新增" [ref=e406]:
                        - generic [ref=e407]: 新增
                      - button "编辑" [ref=e408]:
                        - generic [ref=e409]: 编辑
                      - button "删除" [ref=e410]:
                        - generic [ref=e411]: 删除
                  - group [ref=e412]
            - treeitem "扩展功能 - 新增 编辑 删除" [expanded] [ref=e413]:
              - generic [ref=e414] [cursor=pointer]:
                - img [ref=e416]
                - generic [ref=e418]:
                  - generic [ref=e419]:
                    - img [ref=e421]
                    - generic [ref=e423]: 扩展功能
                  - generic [ref=e424]: "-"
                  - generic [ref=e425]:
                    - button "新增" [ref=e426]:
                      - generic [ref=e427]: 新增
                    - button "编辑" [ref=e428]:
                      - generic [ref=e429]: 编辑
                    - button "删除" [ref=e430]:
                      - generic [ref=e431]: 删除
              - group [ref=e432]:
                - treeitem "代码生成器 /system/generator 新增 编辑 删除" [expanded] [ref=e433]:
                  - generic [ref=e435] [cursor=pointer]:
                    - generic [ref=e436]:
                      - img [ref=e438]
                      - generic [ref=e440]: 代码生成器
                    - generic [ref=e441]: /system/generator
                    - generic [ref=e442]:
                      - button "新增" [ref=e443]:
                        - generic [ref=e444]: 新增
                      - button "编辑" [ref=e445]:
                        - generic [ref=e446]: 编辑
                      - button "删除" [ref=e447]:
                        - generic [ref=e448]: 删除
                  - group [ref=e449]
                - treeitem "多租户管理 /system/tenant 新增 编辑 删除" [expanded] [ref=e450]:
                  - generic [ref=e452] [cursor=pointer]:
                    - generic [ref=e453]:
                      - img [ref=e455]
                      - generic [ref=e459]: 多租户管理
                    - generic [ref=e460]: /system/tenant
                    - generic [ref=e461]:
                      - button "新增" [ref=e462]:
                        - generic [ref=e463]: 新增
                      - button "编辑" [ref=e464]:
                        - generic [ref=e465]: 编辑
                      - button "删除" [ref=e466]:
                        - generic [ref=e467]: 删除
                  - group [ref=e468]
                - treeitem "定时任务 /system/crontab 新增 编辑 删除" [expanded] [ref=e469]:
                  - generic [ref=e471] [cursor=pointer]:
                    - generic [ref=e472]:
                      - img [ref=e474]
                      - generic [ref=e478]: 定时任务
                    - generic [ref=e479]: /system/crontab
                    - generic [ref=e480]:
                      - button "新增" [ref=e481]:
                        - generic [ref=e482]: 新增
                      - button "编辑" [ref=e483]:
                        - generic [ref=e484]: 编辑
                      - button "删除" [ref=e485]:
                        - generic [ref=e486]: 删除
                  - group [ref=e487]
                - treeitem "消息通知 /system/notice 新增 编辑 删除" [expanded] [ref=e488]:
                  - generic [ref=e490] [cursor=pointer]:
                    - generic [ref=e491]:
                      - img [ref=e493]
                      - generic [ref=e497]: 消息通知
                    - generic [ref=e498]: /system/notice
                    - generic [ref=e499]:
                      - button "新增" [ref=e500]:
                        - generic [ref=e501]: 新增
                      - button "编辑" [ref=e502]:
                        - generic [ref=e503]: 编辑
                      - button "删除" [ref=e504]:
                        - generic [ref=e505]: 删除
                  - group [ref=e506]
                - treeitem "文件管理 /system/upload 新增 编辑 删除" [expanded] [ref=e507]:
                  - generic [ref=e509] [cursor=pointer]:
                    - generic [ref=e510]:
                      - img [ref=e512]
                      - generic [ref=e514]: 文件管理
                    - generic [ref=e515]: /system/upload
                    - generic [ref=e516]:
                      - button "新增" [ref=e517]:
                        - generic [ref=e518]: 新增
                      - button "编辑" [ref=e519]:
                        - generic [ref=e520]: 编辑
                      - button "删除" [ref=e521]:
                        - generic [ref=e522]: 删除
                  - group [ref=e523]
            - treeitem "渠道管理 - 新增 编辑 删除" [expanded] [ref=e524]:
              - generic [ref=e525] [cursor=pointer]:
                - img [ref=e527]
                - generic [ref=e529]:
                  - generic [ref=e530]:
                    - img [ref=e532]
                    - generic [ref=e535]: 渠道管理
                  - generic [ref=e536]: "-"
                  - generic [ref=e537]:
                    - button "新增" [ref=e538]:
                      - generic [ref=e539]: 新增
                    - button "编辑" [ref=e540]:
                      - generic [ref=e541]: 编辑
                    - button "删除" [ref=e542]:
                      - generic [ref=e543]: 删除
              - group [ref=e544]:
                - treeitem "微信渠道 /wechat 新增 编辑 删除" [expanded] [ref=e545]:
                  - generic [ref=e546] [cursor=pointer]:
                    - img [ref=e548]
                    - generic [ref=e550]:
                      - generic [ref=e551]:
                        - img [ref=e553]
                        - generic [ref=e556]: 微信渠道
                      - generic [ref=e557]: /wechat
                      - generic [ref=e558]:
                        - button "新增" [ref=e559]:
                          - generic [ref=e560]: 新增
                        - button "编辑" [ref=e561]:
                          - generic [ref=e562]: 编辑
                        - button "删除" [ref=e563]:
                          - generic [ref=e564]: 删除
                  - group [ref=e565]:
                    - treeitem "公众号管理 /wechat/account 新增 编辑 删除" [expanded] [ref=e566]:
                      - generic [ref=e568] [cursor=pointer]:
                        - generic [ref=e569]:
                          - img [ref=e571]
                          - generic [ref=e573]: 公众号管理
                        - generic [ref=e574]: /wechat/account
                        - generic [ref=e575]:
                          - button "新增" [ref=e576]:
                            - generic [ref=e577]: 新增
                          - button "编辑" [ref=e578]:
                            - generic [ref=e579]: 编辑
                          - button "删除" [ref=e580]:
                            - generic [ref=e581]: 删除
                      - group [ref=e582]
                    - treeitem "菜单管理 /wechat/menu 新增 编辑 删除" [expanded] [ref=e583]:
                      - generic [ref=e585] [cursor=pointer]:
                        - generic [ref=e586]:
                          - img [ref=e588]
                          - generic [ref=e590]: 菜单管理
                        - generic [ref=e591]: /wechat/menu
                        - generic [ref=e592]:
                          - button "新增" [ref=e593]:
                            - generic [ref=e594]: 新增
                          - button "编辑" [ref=e595]:
                            - generic [ref=e596]: 编辑
                          - button "删除" [ref=e597]:
                            - generic [ref=e598]: 删除
                      - group [ref=e599]
                    - treeitem "自动回复 /wechat/reply 新增 编辑 删除" [expanded] [ref=e600]:
                      - generic [ref=e602] [cursor=pointer]:
                        - generic [ref=e603]:
                          - img [ref=e605]
                          - generic [ref=e608]: 自动回复
                        - generic [ref=e609]: /wechat/reply
                        - generic [ref=e610]:
                          - button "新增" [ref=e611]:
                            - generic [ref=e612]: 新增
                          - button "编辑" [ref=e613]:
                            - generic [ref=e614]: 编辑
                          - button "删除" [ref=e615]:
                            - generic [ref=e616]: 删除
                      - group [ref=e617]
                    - treeitem "素材中心 /wechat/material 新增 编辑 删除" [expanded] [ref=e618]:
                      - generic [ref=e620] [cursor=pointer]:
                        - generic [ref=e621]:
                          - img [ref=e623]
                          - generic [ref=e626]: 素材中心
                        - generic [ref=e627]: /wechat/material
                        - generic [ref=e628]:
                          - button "新增" [ref=e629]:
                            - generic [ref=e630]: 新增
                          - button "编辑" [ref=e631]:
                            - generic [ref=e632]: 编辑
                          - button "删除" [ref=e633]:
                            - generic [ref=e634]: 删除
                      - group [ref=e635]
                    - treeitem "粉丝管理 /wechat/fans 新增 编辑 删除" [expanded] [ref=e636]:
                      - generic [ref=e638] [cursor=pointer]:
                        - generic [ref=e639]:
                          - img [ref=e641]
                          - generic [ref=e643]: 粉丝管理
                        - generic [ref=e644]: /wechat/fans
                        - generic [ref=e645]:
                          - button "新增" [ref=e646]:
                            - generic [ref=e647]: 新增
                          - button "编辑" [ref=e648]:
                            - generic [ref=e649]: 编辑
                          - button "删除" [ref=e650]:
                            - generic [ref=e651]: 删除
                      - group [ref=e652]
                    - treeitem "小程序 /wechat/mini_program 新增 编辑 删除" [expanded] [ref=e653]:
                      - generic [ref=e655] [cursor=pointer]:
                        - generic [ref=e656]:
                          - img [ref=e658]
                          - generic [ref=e660]: 小程序
                        - generic [ref=e661]: /wechat/mini_program
                        - generic [ref=e662]:
                          - button "新增" [ref=e663]:
                            - generic [ref=e664]: 新增
                          - button "编辑" [ref=e665]:
                            - generic [ref=e666]: 编辑
                          - button "删除" [ref=e667]:
                            - generic [ref=e668]: 删除
                      - group [ref=e669]
                    - treeitem "开放平台 /wechat/open_platform 新增 编辑 删除" [expanded] [ref=e670]:
                      - generic [ref=e672] [cursor=pointer]:
                        - generic [ref=e673]:
                          - img [ref=e675]
                          - generic [ref=e677]: 开放平台
                        - generic [ref=e678]: /wechat/open_platform
                        - generic [ref=e679]:
                          - button "新增" [ref=e680]:
                            - generic [ref=e681]: 新增
                          - button "编辑" [ref=e682]:
                            - generic [ref=e683]: 编辑
                          - button "删除" [ref=e684]:
                            - generic [ref=e685]: 删除
                      - group [ref=e686]
            - treeitem "支付管理 - 新增 编辑 删除" [expanded] [ref=e687]:
              - generic [ref=e688] [cursor=pointer]:
                - img [ref=e690]
                - generic [ref=e692]:
                  - generic [ref=e693]:
                    - img [ref=e695]
                    - generic [ref=e698]: 支付管理
                  - generic [ref=e699]: "-"
                  - generic [ref=e700]:
                    - button "新增" [ref=e701]:
                      - generic [ref=e702]: 新增
                    - button "编辑" [ref=e703]:
                      - generic [ref=e704]: 编辑
                    - button "删除" [ref=e705]:
                      - generic [ref=e706]: 删除
              - group [ref=e707]:
                - treeitem "支付配置 /pay/config 新增 编辑 删除" [expanded] [ref=e708]:
                  - generic [ref=e710] [cursor=pointer]:
                    - generic [ref=e711]:
                      - img [ref=e713]
                      - generic [ref=e715]: 支付配置
                    - generic [ref=e716]: /pay/config
                    - generic [ref=e717]:
                      - button "新增" [ref=e718]:
                        - generic [ref=e719]: 新增
                      - button "编辑" [ref=e720]:
                        - generic [ref=e721]: 编辑
                      - button "删除" [ref=e722]:
                        - generic [ref=e723]: 删除
                  - group [ref=e724]
                - treeitem "订单管理 /pay/order 新增 编辑 删除" [expanded] [ref=e725]:
                  - generic [ref=e727] [cursor=pointer]:
                    - generic [ref=e728]:
                      - img [ref=e730]
                      - generic [ref=e732]: 订单管理
                    - generic [ref=e733]: /pay/order
                    - generic [ref=e734]:
                      - button "新增" [ref=e735]:
                        - generic [ref=e736]: 新增
                      - button "编辑" [ref=e737]:
                        - generic [ref=e738]: 编辑
                      - button "删除" [ref=e739]:
                        - generic [ref=e740]: 删除
                  - group [ref=e741]
                - treeitem "退款管理 /pay/refund 新增 编辑 删除" [expanded] [ref=e742]:
                  - generic [ref=e744] [cursor=pointer]:
                    - generic [ref=e745]:
                      - img [ref=e747]
                      - generic [ref=e751]: 退款管理
                    - generic [ref=e752]: /pay/refund
                    - generic [ref=e753]:
                      - button "新增" [ref=e754]:
                        - generic [ref=e755]: 新增
                      - button "编辑" [ref=e756]:
                        - generic [ref=e757]: 编辑
                      - button "删除" [ref=e758]:
                        - generic [ref=e759]: 删除
                  - group [ref=e760]
                - treeitem "分账管理 /pay/statement 新增 编辑 删除" [expanded] [ref=e761]:
                  - generic [ref=e763] [cursor=pointer]:
                    - generic [ref=e764]:
                      - img [ref=e766]
                      - generic [ref=e769]: 分账管理
                    - generic [ref=e770]: /pay/statement
                    - generic [ref=e771]:
                      - button "新增" [ref=e772]:
                        - generic [ref=e773]: 新增
                      - button "编辑" [ref=e774]:
                        - generic [ref=e775]: 编辑
                      - button "删除" [ref=e776]:
                        - generic [ref=e777]: 删除
                  - group [ref=e778]
            - treeitem "系统工具 - 新增 编辑 删除" [expanded] [ref=e779]:
              - generic [ref=e780] [cursor=pointer]:
                - img [ref=e782]
                - generic [ref=e784]:
                  - generic [ref=e785]:
                    - img [ref=e787]
                    - generic [ref=e789]: 系统工具
                  - generic [ref=e790]: "-"
                  - generic [ref=e791]:
                    - button "新增" [ref=e792]:
                      - generic [ref=e793]: 新增
                    - button "编辑" [ref=e794]:
                      - generic [ref=e795]: 编辑
                    - button "删除" [ref=e796]:
                      - generic [ref=e797]: 删除
              - group [ref=e798]:
                - treeitem "表单设计器 /tool/form-design 新增 编辑 删除" [expanded] [ref=e799]:
                  - generic [ref=e801] [cursor=pointer]:
                    - generic [ref=e802]:
                      - img [ref=e804]
                      - generic [ref=e806]: 表单设计器
                    - generic [ref=e807]: /tool/form-design
                    - generic [ref=e808]:
                      - button "新增" [ref=e809]:
                        - generic [ref=e810]: 新增
                      - button "编辑" [ref=e811]:
                        - generic [ref=e812]: 编辑
                      - button "删除" [ref=e813]:
                        - generic [ref=e814]: 删除
                  - group [ref=e815]
                - treeitem "表单列表 /tool/form-list 新增 编辑 删除" [expanded] [ref=e816]:
                  - generic [ref=e818] [cursor=pointer]:
                    - generic [ref=e819]:
                      - img [ref=e821]
                      - generic [ref=e823]: 表单列表
                    - generic [ref=e824]: /tool/form-list
                    - generic [ref=e825]:
                      - button "新增" [ref=e826]:
                        - generic [ref=e827]: 新增
                      - button "编辑" [ref=e828]:
                        - generic [ref=e829]: 编辑
                      - button "删除" [ref=e830]:
                        - generic [ref=e831]: 删除
                  - group [ref=e832]
                - treeitem "数据大屏 /tool/data-screen 新增 编辑 删除" [expanded] [ref=e833]:
                  - generic [ref=e835] [cursor=pointer]:
                    - generic [ref=e836]:
                      - img [ref=e838]
                      - generic [ref=e842]: 数据大屏
                    - generic [ref=e843]: /tool/data-screen
                    - generic [ref=e844]:
                      - button "新增" [ref=e845]:
                        - generic [ref=e846]: 新增
                      - button "编辑" [ref=e847]:
                        - generic [ref=e848]: 编辑
                      - button "删除" [ref=e849]:
                        - generic [ref=e850]: 删除
                  - group [ref=e851]
            - treeitem "工作流 - 新增 编辑 删除" [expanded] [ref=e852]:
              - generic [ref=e853] [cursor=pointer]:
                - img [ref=e855]
                - generic [ref=e857]:
                  - generic [ref=e858]:
                    - img [ref=e860]
                    - generic [ref=e863]: 工作流
                  - generic [ref=e864]: "-"
                  - generic [ref=e865]:
                    - button "新增" [ref=e866]:
                      - generic [ref=e867]: 新增
                    - button "编辑" [ref=e868]:
                      - generic [ref=e869]: 编辑
                    - button "删除" [ref=e870]:
                      - generic [ref=e871]: 删除
              - group [ref=e872]:
                - treeitem "流程管理 /workflow/list 新增 编辑 删除" [expanded] [ref=e873]:
                  - generic [ref=e875] [cursor=pointer]:
                    - generic [ref=e876]:
                      - img [ref=e878]
                      - generic [ref=e880]: 流程管理
                    - generic [ref=e881]: /workflow/list
                    - generic [ref=e882]:
                      - button "新增" [ref=e883]:
                        - generic [ref=e884]: 新增
                      - button "编辑" [ref=e885]:
                        - generic [ref=e886]: 编辑
                      - button "删除" [ref=e887]:
                        - generic [ref=e888]: 删除
                  - group [ref=e889]
                - treeitem "流程设计 /workflow/design 新增 编辑 删除" [expanded] [ref=e890]:
                  - generic [ref=e892] [cursor=pointer]:
                    - generic [ref=e893]:
                      - img [ref=e895]
                      - generic [ref=e897]: 流程设计
                    - generic [ref=e898]: /workflow/design
                    - generic [ref=e899]:
                      - button "新增" [ref=e900]:
                        - generic [ref=e901]: 新增
                      - button "编辑" [ref=e902]:
                        - generic [ref=e903]: 编辑
                      - button "删除" [ref=e904]:
                        - generic [ref=e905]: 删除
                  - group [ref=e906]
                - treeitem "流程实例 /workflow/instance 新增 编辑 删除" [expanded] [ref=e907]:
                  - generic [ref=e909] [cursor=pointer]:
                    - generic [ref=e910]:
                      - img [ref=e912]
                      - generic [ref=e915]: 流程实例
                    - generic [ref=e916]: /workflow/instance
                    - generic [ref=e917]:
                      - button "新增" [ref=e918]:
                        - generic [ref=e919]: 新增
                      - button "编辑" [ref=e920]:
                        - generic [ref=e921]: 编辑
                      - button "删除" [ref=e922]:
                        - generic [ref=e923]: 删除
                  - group [ref=e924]
                - treeitem "我的待办 /workflow/todo 新增 编辑 删除" [expanded] [ref=e925]:
                  - generic [ref=e927] [cursor=pointer]:
                    - generic [ref=e928]:
                      - img [ref=e930]
                      - generic [ref=e934]: 我的待办
                    - generic [ref=e935]: /workflow/todo
                    - generic [ref=e936]:
                      - button "新增" [ref=e937]:
                        - generic [ref=e938]: 新增
                      - button "编辑" [ref=e939]:
                        - generic [ref=e940]: 编辑
                      - button "删除" [ref=e941]:
                        - generic [ref=e942]: 删除
                  - group [ref=e943]
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | 
  3   | const BASE = 'http://39.105.173.6:8088/admin';
  4   | 
  5   | // 先登录并跳转
  6   | test.beforeEach(async ({ page }) => {
  7   |   await page.goto(`${BASE}/login`);
  8   |   await page.waitForLoadState('networkidle');
  9   |   await page.locator('.el-input__inner').first().fill('admin');
  10  |   await page.locator('.el-input__inner').nth(1).fill('admin123');
  11  |   await page.locator('.login-btn').click();
> 12  |   await page.waitForURL('**/index**', { timeout: 10000 });
      |              ^ TimeoutError: page.waitForURL: Timeout 10000ms exceeded.
  13  | });
  14  | 
  15  | test.describe('布局与侧边栏', () => {
  16  |   test('首页加载完整，侧边栏可见', async ({ page }) => {
  17  |     await expect(page.locator('.sidebar')).toBeVisible();
  18  |     await expect(page.locator('.sidebar-logo')).toBeVisible();
  19  |     await expect(page.locator('.sidebar-logo .logo-text')).toContainText('飞羽后台');
  20  |   });
  21  | 
  22  |   test('折叠按钮存在，可切换侧边栏状态', async ({ page }) => {
  23  |     const collapseBtn = page.locator('.collapse-btn');
  24  |     await expect(collapseBtn).toBeVisible();
  25  | 
  26  |     // 折叠
  27  |     await collapseBtn.click();
  28  |     await expect(page.locator('.sidebar')).toHaveClass(/collapsed/);
  29  | 
  30  |     // 展开
  31  |     await collapseBtn.click();
  32  |     await expect(page.locator('.sidebar')).not.toHaveClass(/collapsed/);
  33  |   });
  34  | 
  35  |   test('顶部头部可见，显示面包屑路径', async ({ page }) => {
  36  |     await expect(page.locator('.header')).toBeVisible();
  37  |     await expect(page.locator('.current-path')).toBeVisible();
  38  |   });
  39  | 
  40  |   test('右侧用户菜单可点击', async ({ page }) => {
  41  |     const userMenu = page.locator('.user-dropdown, .user-info, [class*="user"]').first();
  42  |     await expect(userMenu).toBeVisible();
  43  |   });
  44  | });
  45  | 
  46  | test.describe('系统管理 - 用户管理', () => {
  47  |   test.beforeEach(async ({ page }) => {
  48  |     await page.goto(`${BASE}/system/user`);
  49  |     await page.waitForLoadState('networkidle');
  50  |     await page.waitForTimeout(1000);
  51  |   });
  52  | 
  53  |   // ===== 顶部按钮 =====
  54  |   test('【新增用户】按钮可见可点击，弹出表单对话框', async ({ page }) => {
  55  |     const addBtn = page.locator('.toolbar button').filter({ hasText: '新增' }).first();
  56  |     await expect(addBtn).toBeVisible();
  57  | 
  58  |     await addBtn.click();
  59  |     const dialog = page.locator('.el-dialog');
  60  |     await expect(dialog).toBeVisible();
  61  |     await expect(dialog.locator('.el-dialog__header').filter({ hasText: '新增' })).toBeVisible();
  62  |   });
  63  | 
  64  |   test('【批量删除】按钮存在（选中多条记录后应可用）', async ({ page }) => {
  65  |     const batchDeleteBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '批量删除' });
  66  |     await expect(batchDeleteBtn.first()).toBeVisible();
  67  |   });
  68  | 
  69  |   test('【导出】按钮可见可点击', async ({ page }) => {
  70  |     const exportBtn = page.locator('.toolbar button, .action-bar button').filter({ hasText: '导出' });
  71  |     await expect(exportBtn.first()).toBeVisible();
  72  |     await exportBtn.first().click();
  73  |   });
  74  | 
  75  |   test('【刷新】按钮可见可点击', async ({ page }) => {
  76  |     const refreshBtn = page.locator('button').filter({ hasText: '刷新' });
  77  |     await expect(refreshBtn.first()).toBeVisible();
  78  |   });
  79  | 
  80  |   // ===== 搜索栏按钮 =====
  81  |   test('【搜索】按钮可点击，数据重新加载', async ({ page }) => {
  82  |     const searchBtn = page.locator('button').filter({ hasText: '搜索' });
  83  |     await expect(searchBtn).toBeVisible();
  84  |     await searchBtn.click();
  85  |   });
  86  | 
  87  |   test('【重置】按钮可点击，清空搜索表单', async ({ page }) => {
  88  |     const resetBtn = page.locator('button').filter({ hasText: '重置' });
  89  |     await expect(resetBtn).toBeVisible();
  90  |     await resetBtn.click();
  91  |   });
  92  | 
  93  |   test('状态筛选下拉框可用', async ({ page }) => {
  94  |     const select = page.locator('.el-select').first();
  95  |     await expect(select).toBeVisible();
  96  |     await select.click();
  97  |     await expect(page.locator('.el-select-dropdown')).toBeVisible();
  98  |   });
  99  | 
  100 |   // ===== 表格列按钮 =====
  101 |   test('【编辑】按钮可见可点击，弹出编辑对话框', async ({ page }) => {
  102 |     // 等待表格加载
  103 |     await page.waitForSelector('.el-table__row', { timeout: 5000 });
  104 |     const editBtn = page.locator('.el-table__row .el-button').filter({ hasText: '编辑' }).first();
  105 |     await expect(editBtn).toBeVisible();
  106 |     await editBtn.click();
  107 | 
  108 |     const dialog = page.locator('.el-dialog');
  109 |     await expect(dialog).toBeVisible();
  110 |     await expect(dialog.locator('.el-dialog__header').filter({ hasText: '编辑' })).toBeVisible();
  111 |   });
  112 | 
```