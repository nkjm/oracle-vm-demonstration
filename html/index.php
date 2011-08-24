<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Oracle Database Cloud" />
<title>Oracle VM & ZFS Demonstration</title>
<link rel='stylesheet' type='text/css' href='/css/Cuprum.css' >
<link rel="stylesheet" type="text/css" href="/css/oracle.css" />
<link rel="stylesheet" type="text/css" href="/jquery.confirm/jquery.confirm.css" />

</head>
<body>

<?php
if ($error->skip == TRUE) {
    goto start_js;
}
?>

<div id=top style='text-align:right; padding: 20px 10px 10px 0;'>
    <h1><a href='/' style='text-decoration:none; padding: 4px 0 0 0;'>Oracle VM & ZFS Demonstration</a></h1>
</div>
<div id=middle>
    <div id=cloud_status style='margin: 0 auto 0 auto; width: 95%;'>
        <div id=vmserver class=cloud_status>
            <h3>VM Server</h3>
            <div style='padding-top:10px;'>
            <?php require_once 'vmserver.php'; ?>
            </div>
        </div>
        <div id=storage class=cloud_status>
            <h3>Storage</h3>
            <div style='padding-top:10px;'>
            <?php require_once 'storage.php';?>
            </div>
        </div>
    </div>
    <div id=vm style='clear:both; padding: 20px 0 0 0;'>
        <div id=new_vm style='width:20%; margin:0 auto 0 auto;'>
            <img class=new src='/img/new.png' style='float:left;'></img><span class=new style='float:left; margin-top:10px; font-size:0.9em; font-weight: bold;'>New VM</span>
        </div>
        <div id=new_vm_form style='display:none; font-size:1.2em; clear:both; width:40%; margin:50px auto 10px auto;'>
            <?php require_once 'new_vm_form.php'; ?>
        </div>
        <div id=existing_vm style='clear:both; padding: 10px 0 0 0;'>
            <?php require_once 'existing_vm.php'; ?>
        </div>
    </div>
</div>
<div id=bottom style='padding: 20px 0 0 0;'>
    &nbsp;
</div>

<?php
start_js:
?>

<script src="/jquery/jquery.min.js"></script>
<script src="/jquery.confirm/jquery.confirm.js"></script>
<script src="/jquery.activity_indicator/jquery.activity-indicator-1.0.0.js"></script>
<script src="/jquery.corner/jquery.corner.js"></script>
<script src="/js/script.js"></script>
</body>
</html>

