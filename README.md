这是汇承红外模块服务器

数据库：MYSQL
缓存：REDIS
MACID: 为12位固定id+4位随机数
CLIENTID: 为固定20字符

数据与APP传输协议

注册macid

{'MsgType': 'Register', 'Content': ''}

心跳包

{'MsgType': 'HearPacket', 'Content': ''}

更新蓝牙标志

{'MsgType': 'BuleSign', 'Content': ''}

更新按键名称

{'MsgType': 'Button', 'Content': ''}

数据传输

{'MsgType': 'Trans', 'ClientId': '', 'Content': ''}

数据传输成功(检测APP和服务器是否连接、检测蓝牙是否连接)

{'MsgType': 'Trans', 'ClientId': '', 'Content': 001}

APP与服务器断开连接