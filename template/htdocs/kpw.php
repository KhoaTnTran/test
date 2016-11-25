<?
include 'lb.php';
if ($usehttps) include 'https.php';

include 'connect.php';
include 'settings.php';

include 'evict.php';
evict_check();

login();
$user=userinfo();
?>
<html>
<head>
	<title><?echo GYROSCOPE_PROJECT;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
	<link href='iphone/gyrodemo.css' type='text/css' rel='stylesheet'>
	<link href='toolbar_kpw.css' type='text/css' rel='stylesheet'>
	<link href='iphone/kpw.css' type='text/css' rel='stylesheet'>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	
	<?
	include 'appicon.php';
	?>
<style>
body{font-family:helvetica;}
.menuitem{padding-left:10px;height:30px;float:left;margin-right:3px;}
.menuitem a, .menuitem a:hover, .menuitem a:visited, .menuitem a:link{
	display:block;
	padding-top:3px;
	color:#000000;
	text-decoration:none;
}

</style>

</head>
<body onload="setTimeout(scrollTo, 0, 0, 1);">

<div id="toolbg" style="position:fixed;width:100%;z-index:1000;top:0;background:#000000;"></div>
<div id="toolicons" style="position:fixed;width:100%;z-index:2000;top:0;">

	<div id="toollist" style="overflow:auto;width:100%;"><div style="width:<?echo 50*(count($toolbaritems)+2);?>px;">

	<?foreach ($toolbaritems as $modid=>$ti){
		if ($ti['type']=='break') continue;
		if ($ti['noiphone']) continue;	
		if ($ti['type']=='custom'){
		?>
		<?echo $ti['iphone'];?>
		<?	
			continue;
		}
		
		$action="showview('".$modid."',null,1);";
		if ($ti['action']!='') $action=$ti['action'];
		if (!isset($ti['icon'])||$ti['icon']=='') continue;
		
		if (isset($ti['groups'])){
			$canview=0;
			$gs=explode('|',$ti['groups']);
			foreach ($gs as $g) if (isset($user['groups'][$g])) $canview=1;
			if (!$canview) continue;	
		}
		
	?>
	<div class="menuitem"><a href=# onclick="<?echo $action;?>return false;"><img class="<?echo $ti['icon'];?>" src="imgs/t.gif" border="0" width="64" height="64"></a></div>
	<?}?>

	</div></div>
	<span id="labellogin" style="display:none;"><?echo $user['login'];?></span><span id="labeldispname" style="display:none;"><?echo $user['dispname'];?></span>	
	<a href="login.php?from=<?echo $_SERVER['PHP_SELF'];?>" style="position:absolute;top:20px;right:30px;"><img border="0" width="32" height="32" src="imgs/t.gif" class="admin-logout"></a>
</div><!-- toolicons -->
<div id="pusher" style="width:100%;height:100px;"></div>

<div style="display:none;">
	<img src="imgs/t.gif"><img src="imgs/hourglass.gif">
</div>
<div id="leftview" style="float:left;margin-left:10px;width:100%;">
	<div id="tooltitle" style="width:100%;position:fixed;top:100px;z-index:1000;height:50px;"></div>
	<div id="tooltitleshadow" style="width:100%;height:50px;"></div>
	<div id="lvviews">
	<?foreach ($toolbaritems as $modid=>$ti){?>
		<div id="lv<?echo $modid;?>" style="background-color:#ffffff;display:none;"></div>
	<?}?>	
	</div>
	<div id="lkv" style="height:100%;">
		<div id="lkvtitle"><a id="lkvt"></a><img id="lkvx" src="imgs/t.gif" onclick="hidelookup();" width="30" height="24"></div>
		<div id="lkvc"></div>
	</div>
	
</div>
<div id="content" style="float:left;width:320px;">

	<div id="backlist" style="display:none;position:fixed;left:0;width:100%;z-index:1000;"><a id="backlistbutton"><img onclick="navback();" src="iphone/back_kpw.png"></a></div>
	<div id="backlistshadow" style="display:none;width:100%;"></div>

	<div id="tabtitles" style="width:325px;position:fixed;z-index:1000;"></div>
	<div id="tabtitleshadow" style="height:25px;width:100px;display:none;"></div>

	<div id="tabviews" style=""></div>
	<div id="statusinfo" style="display:none;"><div id="statusc"></div></div>
</div>
<div id="rotate_indicator" style="display:none;position:fixed;width:100px;height:100px;top:220px;left:110px;z-index:3000;"></div>
<div id="fsmask"></div>
<div id="fstitlebar">
	<div id="fstitle"></div>
	<a id="fsclose" onclick="closefs();"><img width="20" height="20" class="img-closeall" src="imgs/t.gif"></a>
</div>
<div id="fsview"></div>

<script>
document.appsettings={codepage:'<?echo $codepage;?>',fastlane:'<?echo $fastlane;?>', views:<?echo json_encode(array_keys($toolbaritems));?>};
</script>
<script src="lang/dict.<?echo $lang;?>.js"></script>
<script src="nano.js"></script>
<script src="iphone/tabs.js"></script>
<script src="iphone/viewport.js"></script>
<script src="validators.js"></script>
<script src="autocomplete.js"></script>

<script>

function showdeck(){
	switch(document.viewmode){
		
		case 1: 
			gid('leftview').style.display='block'; 
			gid('tabtitles').style.display='block';
			
			gid('content').style.display='none';

		break;
		case 2:
			gid('leftview').style.display='none'; 
			gid('tabtitles').style.display='none';
			
			gid('content').style.display='block';
			
		break;
	}
		
}


function rotate(){
	

	
	if (!document.appsettings.cw) document.appsettings.cw=320;
	if (document.appsettings.cw<document.body.clientWidth) document.appsettings.cw=document.body.clientWidth;
		
	var cw=document.appsettings.cw;
	var vw=document.body.clientWidth;
	
		
		showdeck();
		gid('leftview').style.width=vw+'px';
		gid('leftview').style.marginLeft=0;
		gid('backlist').style.display='block';
		gid('backlistshadow').style.display='block';
		gid('tooltitle').style.width=vw+'px';
		gid('toollist').style.width=document.documentElement.clientWidth-50+'px';//'280px';
		gid('tabtitleshadow').style.display='none';
		gid('content').style.width=vw+'px';
		
		document.viewheight=vw+30;
		scaleall(document.body);
		document.iphone_portrait=1;
		
		hidelookup();
		
	
}

addtab('welcome','<?tr('tab_welcome');?>','wk',null,null,{noclose:true});

function onrotate(){
	if (document.resizetimer) clearTimeout(document.resizetimer);
	document.resizetimer=setTimeout(function(){
		rotate();
		setTimeout(rotate,500);
	},100);
}

setInterval(authpump,60000); //check if needs to re-login; comment this out to disable authentication

addtab('welcome','Welcome','wk',null,null,{noclose:true});

//override
function scaleall(root){
  var i,j;
  var idh=ch();
  var idw=cw();
  
  var os=root.getElementsByTagName('div'); //AKB#2
  
	gid('tabviews').style.height=(idh-210)+'px';
	gid('lvviews').style.height=(idh-210)+'px';

  if (document.rowcount){
		gid('tabtitleshadow').style.height=(56*document.rowcount-1)	  
  }

  gid('lkv').style.height=(idh-145)+'px';
  gid('lkvc').style.height=(idh-150)+'px';
  
  gid('fsmask').style.width=idw+'px';
  gid('fsmask').style.height=idh+'px';

  gid('fsview').style.width=idw-40+'px';
  gid('fsview').style.height=idh-100+'px';
  
  gid('fstitlebar').style.width=idw-40+'px';   
  	   
}

scaleall(document.body);

</script>
<?
//Speech and WSS features are removed for book readers
?>
<script src="tiny_mce/mceloader.js"></script>
</body>
</html>
