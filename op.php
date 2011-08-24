<?php

/*
 * This software cannot be modified, embedded or redistributed without the author's permision.
 * Copyright Kazuki Nakajima <nkjm.kzk@gmail.com>
 */

set_time_limit(300);
ini_set('display_errors', 'Off');
ini_set('date.timezone', 'Asia/Tokyo');

require_once './config.php';
require_once './Error.php';
require_once './Parse.php';
require_once './Vmservers.php';
require_once './Zfs.php';
require_once './Guests.php';
$error = new Error();
$parse = new Parse();


/***
 *** Sanitize
 ***/

if (isset($_REQUEST["vm_name"])) {
    $vm_name = $parse->vm_name($_REQUEST["vm_name"]);
    $error->check();
}
if (isset($_REQUEST["template_name"])) {
    $template_name = $parse->template_name($_REQUEST["template_name"]);
    $error->check();
}
if (isset($_REQUEST["vm_password"])) {
    $vm_password = $parse->vm_password($_REQUEST["vm_password"]);
    $error->check();
}
if (isset($_REQUEST["vm_ip"])) {
    $vm_ip = $parse->vm_ip($_REQUEST["vm_ip"]);
    $error->check();
}
if (isset($_REQUEST["vm_cpu"])) {
    $vm_cpu = $parse->vm_cpu($_REQUEST["vm_cpu"]);
    $error->check();
}
if (isset($_REQUEST["vm_memory"])) {
    $vm_memory = $parse->vm_memory($_REQUEST["vm_memory"]);
    $error->check();
}
if (isset($_REQUEST["vm_dedicated_nfs_size"])) {
    $vm_dedicated_nfs_size = $parse->vm_dedicated_nfs_size($_REQUEST["vm_dedicated_nfs_size"]);
    $error->check();
}
if (isset($_REQUEST["vm_snapshot_name"])) {
    $vm_snapshot_name = $parse->vm_snapshot_name($_REQUEST["vm_snapshot_name"]);
    $error->check();
}
if (isset($_REQUEST["op"])) {
    $op = $_REQUEST["op"];
} else {
    $op = null;
}


/***
 *** Operation
 ***/

switch ($op) {
    case 'vm_create':
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $zfs->create_vm($vm_name, $template_name, $vm_ip);
        $error->check();
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        if ($template_name == 'Oracle_Linux_5') {
            $vm_os = 'Oracle Enterprise Linux 5 64-bit';
        } elseif ($template_name == 'Oracle_Solaris_11exp' || $template_name == 'Oracle_Solaris_10') {
            $vm_os = 'Oracle Solaris 10';
        } else {
            $vm_os = 'Other';
        }
        $guests->import_vm($vm_name, $vm_password, $vm_os);
        $error->check();
        break;
    case 'vm_delete':
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        $vm_power = $guests->get_vm_power($vm_name);
        $guests->delete_vm($vm_name, $vm_power);
        $error->check();
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $zfs->delete_vm($vm_name);
        $error->check();
        break;
    case 'vm_switch_power':
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        $vm_power = $guests->get_vm_power($vm_name);
        $result = $guests->switch_power($vm_name, $vm_power);
        $error->check();
        break;
    case 'vm_update_cpu':
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        $guests->update_cpu($vm_name, $vm_cpu);
        $error->check();
        break;
    case 'vm_update_memory':
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        $guests->update_memory($vm_name, $vm_memory);
        $error->check();
        break;
    case 'vm_update_dedicated_nfs_size':
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $zfs->update_dedicated_nfs_size($vm_name, $vm_dedicated_nfs_size);
        $error->check();
        break;
    case 'vm_update_disk':
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $zfs->update_dedicated_nfs($vm_name, $vm_disk);
        $error->check();
        break;
    case 'vm_snapshot':
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $vm_snapshot_name = date("Y_m_d_H_i_s");
        $zfs->snapshot_vm($vm_name, $vm_snapshot_name);
        $error->check();
        break;
    case 'vm_rollback':
        $guests = new Guests(OVM_USER, OVM_PASSWORD, OVM_POOL);
        $vm_power = $guests->get_vm_power($vm_name);
        $zfs = new Zfs(ZFS_USER, ZFS_HOSTNAME);
        $zfs->rollback_vm($vm_name, $vm_snapshot_name, $vm_power);
        $error->check();
        break;
}
$error->flush();
?>

