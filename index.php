<?php

/*
 * This software cannot be modified, embedded or redistributed without the author's permision.
 * Copyright Kazuki Nakajima <nkjm.kzk@gmail.com>
 */

set_time_limit(300);
//ini_set('display_errors', 'Off');
ini_set('date.timezone', 'Asia/Tokyo');

require_once './config.php';
require_once './Error.php';
require_once './Parse.php';
require_once './Vmservers.php';
require_once './Zfs.php';
require_once './Guests.php';
$error = new Error();
$parse = new Parse();
$zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
$vmservers = new Vmservers(OVM_USER, OVM_PASSWORD, OVM_POOL);
$guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL, ZFS_USER, ZFS_HOSTNAME);

$ovs_list = $vmservers->get_ovs_list();
$zpool = $zfs->get_zpool_detail(ZFS_ZPOOL);
#$template_list = $zfs->get_template_list();
$vm_list = $guests->get_vm_list($ovs_list);

start_html:

$error->check(FALSE, 'html');
require_once 'html/index.php';
?>

