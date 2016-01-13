# 这是汇承红外模块服务器

数据库：MYSQL

缓存：REDIS

MACID: 为12位固定id+4位随机数

CLIENTID: 为固定20字符

## websocket与服务器传输协议

### 按下按键(websocket→服务器)

{'MsgType': 'Trans', 'MacId': '', 'Content': ''}

### 传输结果(服务器→websocket)

{'MsgType': 'Trans', 'ButtonId': '', 'Content': ''}



## APP与服务器传输协议


### 注册macid(APP→服务器)

{'MsgType': 'Register', 'Content': ''}

eg: {'MsgType': 'Register', 'Content': '1234567890654321'}


### 心跳包(APP→服务器)

{'MsgType': 'HeartPacket', 'Content': ''}

eg: {'MsgType': 'HeartPacket', 'Content': 1}


### 蓝牙标志(APP→服务器)

{'MsgType': 'BlueSign', 'Content': ''}

eg: {'MsgType': 'BlueSign', 'Content': 1}


### 初始化按键(APP→服务器)

{'MsgType': 'iniButton', 'Content': ''}

eg: {'MsgType': 'iniButton', 'Content': '[1],[2],[3],[4],...'}

### 更新按键名称(APP→服务器)

{'MsgType': 'altButton', 'Content': ''}

eg: {'MsgType': 'Button', 'Content': '2/关'}


### 数据传输(服务器→APP)

{'MsgType': 'Trans', 'ClientId': '', 'Content': ''}

eg: {'MsgType': 'Trans', 'ClientId': '1234567890123456', 'Content': 2}


### 数据传输成功(APP→服务器)

{'MsgType': 'Trans', 'ClientId': '', 'ButtonId': '', 'Content': '001'}

eg: {'MsgType': 'Trans', 'ClientId': '1234567890123456', 'ButtonId': 2, 'Content': '001'}


### APP与服务器断开连接(APP→服务器)

{'MsgType': 'Trans', 'ClientId': '', 'ButtonId': '', 'Content': '101'}

eg: {'MsgType': 'Trans', 'ClientId': '1234567890123456', 'ButtonId': 2, 'Content': '101'}


### APP与蓝牙设备断开连接(APP→服务器)

{'MsgType': 'Trans', 'ClientId': '', 'ButtonId': '', 'Content': '102'}

eg: {'MsgType': 'Trans', 'ClientId': '1234567890123456', 'ButtonId': 2, 'Content': '102'}