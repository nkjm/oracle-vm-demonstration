#!/usr/bin/python

### User Configurable Parameters
zfs_ip = ""
vmserver_list = [""]
repository_name = ""
dedup = "off"
dedicated_nfs_size = "10G"
snapshot_retention = 5

### Do not edit below
repository_root = "rpool/virtzfs/%s" % repository_name
iqn_base = "iqn.2011-06.virtzfs.%s" % repository_name
comstar_hostgroup_name = "nkjm"
shareddisk_prefix = "sdisk"
dir_domain = "nfs/running_pool"
dir_vm = "running_pool"
dir_template = "seed_pool"
dir_shareddisk = "sharedDisk"
dir_dedicated_nfs = "nfs/dedicated"
msg_success = "[SUCCEEDED]"
msg_fail = "[FAILED]"
