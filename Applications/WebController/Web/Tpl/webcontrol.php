<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))).'/Workerman/Autoloader.php';
\Workerman\Autoloader::setRootPath(__DIR__.'/../../');
use \Workerman\Protocols\Http;
use \GatewayWorker\Lib\Db;
if(!isset($_GET['macid'])){

	echo "对不起，你访问的页面不存在！";
	HTTP::end();
}
// error_reporting(0);
$macid = $_GET['macid'];
$connectHC = Db::instance('ConnectHC');
$result = $connectHC->single("SELECT param FROM `WEBHC` WHERE macid='$macid'");
$param = explode('],[', substr(substr($result, 1), 0, -1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title></title>
  <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/buttons.css">
  <link rel="stylesheet" href="css/messenger.css">
  <link rel="stylesheet" href="css/messenger-theme-block.css">
  <link rel="stylesheet" href="css/hcweb.css">
</head>
<body>
<!-- 主页模态框开始 -->
<div class="modal fade" id="homeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">              
              <h4 class="modal-title" id="myModalLabel">是否跳转到广州汇承淘宝旗舰店</h4>
          </div>
          <div class="modal-body"><a href="https://hc-com.taobao.com">https://hc-com.taobao.com</a></div>
          <div class="modal-footer" style="text-align: center;">
            <button type="button" name="islocation" class="button button-action button-pill" style="height: 30px; line-height: 30px;">确定</button>&nbsp;
              <button type="button" class="button button-action button-caution button-pill" style="height: 30px; line-height: 30px;" data-dismiss="modal">取消</button>
          </div>
      </div>
  </div>
</div>
<!-- 主页模态框结束 -->
<!-- 信息模态框开始 -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body" style="text-align: center; font-size: 20px;">
            <div style="margin-bottom: 10px;">
              <img src="img/LOGO.png" alt="..." class="img-circle">
            </div>
            <p><span class="glyphicon glyphicon-user"></span>&nbsp;ID: <?php echo $macid; ?></p>
            <address style="font-size: 15px;">
              地址：广州天河区建工路<br>
              科韵路19号608室
            </address>
            <p style="font-size: 15px;">电话：18028699442</p>
            <p style="font-size: 15px;">技术QQ：1508128262</p>
          </div>
        </div>
    </div>
</div>
<!-- 信息模态框结束 -->
<!-- 帮助模态框开始 -->
<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body" style="text-align: center;">
            <h2>HELP</h2>
            <h2>HELP</h2>
          </div>
        </div>
    </div>
</div>
<!-- 帮助模态框结束 -->
<div class="container-fluid" style="margin-bottom: 50px;">
  <div class="col-md-8 col-md-offset-2" style="padding-left: 0px; padding-right: 0px;">
  <?php  for($i=0; $i<30; $i++){?>
  	<div class="col-xs-4 col-sm-4 col-md-4 hcbtnbox">
  	<button class="hcbtn" id="<?php echo $i+1?>"><?php echo $param[$i]?></button>
  	</div>
  <?php  }  ?>
</div>
</div>
<div class="hcbottombox">
  <div class="col-md-8 col-md-offset-2">
  <div class="col-xs-4 col-sm-4 col-md-4 hcbottomtag"><span class="glyphicon glyphicon-shopping-cart" id="home"></span></div>
  <div class="col-xs-4 col-sm-4 col-md-4 hcbottomtag"><span class="glyphicon glyphicon-user" id="info"></span></div>
  <div class="col-xs-4 col-sm-4 col-md-4 hcbottomtag"><span class="glyphicon glyphicon-question-sign" id="question"></span></div>
  </div>
</div>
</body>
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/messenger.min.js"></script>
<script src="js/messenger-theme-future.js"></script>
<script src="js/swfobject.js"></script>
<script src="js/web_socket.js"></script>
<script>
  $(function(){

    var intelsign = false;
    WEB_SOCKET_SWF_LOCATION = "js/WebSocketMain.swf";
    var ws = new WebSocket("ws://112.74.74.150:4404/");
    
    // websocket握手事件
    ws.onopen = function() {
      intelsign = true;
    };

    // websocket接收数据事件
    ws.onmessage = function(e) {

      var data = Array();
      var dc = document;
      var a = e.data.substring(1, e.data.indexOf('}'));
      var result = a.split(',');
      for(var d in result)
      {
        var c = result[d].split(':');
        data[c[0]] = c[1];
      }
      var ele = dc.getElementById(data['ButtonId']);
      var content = data['Content'];
      ele.className = "hcbtn";
      switch(content)
      {
        case '001':
          Messenger().post({
            message: "发送成功！",
            type: "success",
            hideAfter: 1,
            hideOnNavigate: true,
          });
          break;

        case '101':
          Messenger().post({
            message: "设备与服务器断开，请稍后重试！",
            type: "error",
            hideAfter: 1,
            hideOnNavigate: true,      
          });
          break;

        case '102':
          Messenger().post({
            message: "设备与红外蓝牙断开，请稍后重试！",
            type: "error",
            hideAfter: 1,
            hideOnNavigate: true,      
          });
          break;

        default:
          Messenger().post({
            message: "发送超时，请稍后重试！",
            type: "error",
            hideAfter: 1,
            hideOnNavigate: true,     
          });
          break;
      }
    };

    // websoket断开事件
    ws.onclose = function() {
      if(!intelsign)
      {
        return;
      }
      intelsign = false;
      Messenger().post({
        message: "与服务器连接断开，请刷新页面重试！",
        type: "error",
        hideAfter: 1,
        hideOnNavigate: true,
      });
    };


    var macid = "<?php echo $macid ?>";
    var w = window;
    // 初始化弹出框
    Messenger.options = {
    extraClasses: 'messenger-fixed messenger-on-bottom',
    theme: 'block'
    }

    // 菜单栏按下
    $("span").on("touchstart touchend click", function(){

      var dc = document;
      var eventype = event.type;
      var elementid = $(this).attr("id");
      if(eventype == "touchstart"){
        $(this).css("color", "#fff");
      }
      if(eventype == "touchend"){
        $(this).css("color", "#0888e6");
      }
      if(eventype == "click"){
        switch(elementid){
          case "home":
            $('#homeModal').modal('show');
          break;
          case "info":
            $('#infoModal').modal('show');
          break;
          case "question":
            $('#questionModal').modal('show');
          break;

          default:
            break;
        }
      }
    });

    // 跳转淘宝主页
    $("button[name='islocation']").click(function(){

      w.location.href="https://hc-com.taobao.com";
    });

    // 用户操作按键
    $(".hcbtn").click(function(){
      if(!intelsign)
      {
        Messenger().post({
        message: "与服务器连接断开，请刷新页面重试！",
        type: "error",
        hideAfter: 1,
        hideOnNavigate: true,
        });
        return;
      }
      var ele = $(this)[0];
      ele.className = "hcbtnactive";
      var buttonid = $(this).attr("id");
      var transpacket = '{"MsgType": "Trans", "MacId": "' + macid + '", "Content": ' + buttonid + '}';
      // websocket发送
      ws.send(transpacket);
    });
  })
</script>