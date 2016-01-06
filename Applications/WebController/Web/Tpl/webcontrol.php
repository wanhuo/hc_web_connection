<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))).'/Workerman/Autoloader.php';
\Workerman\Autoloader::setRootPath(__DIR__.'/../../');
use \Workerman\Protocols\Http;
use \GatewayWorker\Lib\Db;
if(!isset($_GET['macid'])){

	echo "对不起，你访问的页面不存在！";
	HTTP::end();
}
$macid = $_GET['macid'];
$connectHC = Db::instance('ConnectHC');
$result = $connectHC->row("SELECT param FROM `WEBHC` WHERE macid='$macid'");
$param = explode('/', $result['param']);
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
  <?php  for($i=0; $i<30; $i++){?>
  	<div class="col-xs-4 col-sm-4 col-md-4" style="margin: 10px 0; text-align: center; padding: 0px;">
  	<button class="button button-raised button-primary button-pill" style="max-width: 250px; width: 95%; padding: 0px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" id="<?php echo $i+1?>"><?php echo $param[$i]?></button>
  	</div>
  <?php  }  ?>
</div>
<div style="height: 50px; width: 100%; background-color: rgba(245, 245, 245, 0.95); color: #0888e6; z-index: 999px; position:fixed; bottom: 0px;">
  <div class="col-xs-4 col-sm-4 col-md-4" style="height: 100%; line-height: 50px; text-align: center; padding: 0px; padding-top: 10px;"><span class="glyphicon glyphicon-shopping-cart" id="home" style="font-size: 30px;"></span></div>
  <div class="col-xs-4 col-sm-4 col-md-4" style="height: 100%; line-height: 50px; text-align: center; padding: 0px; padding-top: 10px;"><span class="glyphicon glyphicon-user" id="info" style="font-size: 30px;"></span></div>
  <div class="col-xs-4 col-sm-4 col-md-4" style="height: 100%; line-height: 50px; text-align: center; padding: 0px; padding-top: 10px;"><span class="glyphicon glyphicon-question-sign" id="question" style="font-size: 30px;"></span></div>
</div>
</body>
<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/messenger.min.js"></script>
<script src="js/messenger-theme-future.js"></script>
<script>
  $(function(){

    var macid = "<?php echo $macid ?>";
    // 初始化弹出框
    Messenger.options = {
    extraClasses: 'messenger-fixed messenger-on-bottom',
    theme: 'block'
    }

    $("span").on("touchstart touchend click", function(){

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
        }
      }
    });

    $("button[name='islocation']").click(function(){

      window.location.href="https://hc-com.taobao.com";
    });

    $("button.button-primary").click(function(){
      var buttonid = $(this).attr("id");
      // AJAX异步发送
      $.ajax({
        url: "webcontrol_admin.php",
        type: "POST",
        data: {'macid' : macid, 'buttonid' : buttonid},
        success: function(data){
          if(data == "101"){
            Messenger().post({
              message: "发送成功！",
              type: "success",
              hideAfter: 1,
              hideOnNavigate: true,
            });
            return;
          }
          if(data == "102"){
            Messenger().post({
              message: "设备与服务器断开，请稍后重试！",
              type: "error",
              hideAfter: 1,
              hideOnNavigate: true,      
            });
            return;
          }
          if(data == "103"){
            Messenger().post({
              message: "设备与红外蓝牙断开，请稍后重试！",
              type: "error",
              hideAfter: 1,
              hideOnNavigate: true,      
            });
            return;
          }

        },
        error: function(data){
          Messenger().post({
            message: "发送超时，请稍后重试！",
            type: "error",
            hideAfter: 2,
            hideOnNavigate: true,     
          });
        }
      })
    });
  })
</script>