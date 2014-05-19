<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head><title>COPRONet</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<style type="text/css">
body {
	font-family:Arial,Helvetica,Sans-serif;
	font-size:12px;
	background-color: #FFF;
	background-image:url('web/img/login-bg.gif');
	background-repeat:repeat-x;
	background-position:center center;
	background-attachment:fixed;
	margin:0px;
	color: #000;
}
form { margin:0; }
.splashscreen {
	position:absolute;
	top:50%;
	left:50%;
	width:400px;
	height:200px;
	margin-top:-100px;
	margin-left:-200px;
	border:2px solid #1e2834;
	background:url('web/img/splash-screen.jpg');
}
.splashscreen input {
	position:relative;
}
.splashscreen input[type=text], .splashscreen input[type=password] {
	border:1px solid #888;
	background-color:#fff;
	width:200px;
	padding-left:20px;
}
.splashscreen #ccid {
	top:43px;
	left:150px;
	background-image:url('web/img/logincust.gif');
	background-repeat:no-repeat;
	background-position:2px 2px;
}
.splashscreen #cuid {
	top:58px;
	left:150px;
	background-image:url('web/img/loginuser.gif');
	background-repeat:no-repeat;
	background-position:6px 4px;
}
.splashscreen #cpwd {
	top:73px;
	left:150px;
	background-image:url('web/img/loginpw.gif');
	background-repeat:no-repeat;
	background-position:3px 4px;
}
.splashscreen #btnLogin {
	top:90px;
	left:150px;
	background-color:#1e2834;
	height:25px;
	width:120px;
	color:#fff;
	border-top:1px solid #90b3d4;
	border-right:1px solid #666;
	border-bottom:1px solid #666;
	border-left:1px solid #90b3d4;
	font-weight:bold;
}
</style><script type="text/javascript" charset="UTF-8">
/* <![CDATA[ */
try { if (undefined == xajax.config) xajax.config = {}; } catch (e) { xajax = {}; xajax.config = {}; };
xajax.config.requestURI = "dispatcher-ui.php";
xajax.config.statusMessages = false;
xajax.config.waitCursor = true;
xajax.config.version = "xajax 0.5 Beta 4";
xajax.config.legacy = false;
xajax.config.defaultMode = "asynchronous";
xajax.config.defaultMethod = "POST";
/* ]]> */
</script><script type="text/javascript" src="web/xajax_js/xajax_core.js" charset="UTF-8"></script><script type="text/javascript" charset="UTF-8">
/* <![CDATA[ */
window.setTimeout(
 function() {
  var scriptExists = false;
  try { if (xajax.isLoaded) scriptExists = true; }
  catch (e) {}
  if (!scriptExists) {
   alert("Error: the xajax Javascript component could not be included. Perhaps the URL is incorrect?\nURL: xajax_js/xajax_core.js");
  }
 }, 2000);
cb = function() { return xajax.request( { xjxfun: 'dispatcher' }, { parameters: arguments } ); };
/* ]]> */
</script><meta http-equiv="content-type" content="text/html;charset=utf-8"/>

</head><body id="htmlBody">
<div class="splashscreen">
<form id="loginform" action="javascript:void(0);" onsubmit="cb('core.login',xajax.getFormValues('loginform'));">
<input type="text" id="ccid" name="ccid" value=""/><br/>
<input type="text" id="cuid" name="cuid" value=""/><br/>
<input type="password" id="cpwd" name="cpwd" value=""/><br/>
<input type="submit" id="btnLogin" name="btnLogin" value="LOGIN" />
</form>
</div>
<script type="text/javascript" charset="UTF-8">
/* <![CDATA[ */
document.getElementById('ccid').focus();
/* ]]> */
</script>
</body></html>
