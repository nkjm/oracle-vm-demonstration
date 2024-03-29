#!/usr/bin/python

import commands
import copy
import optparse
import os
import shutil
import socket
import string
import sys
import tempfile
import random
import re
import time

from config import *
from utility import Utility
utility = Utility()

class Domain:
    def __init__(self):
        self.ip = 'n/a'
        self.hostname = 'n/a'
        self.volume_list = []  
        self.network_list = []
        self.shareddisk_list = []
        self.targetname = self.get_targetname()
        self.targetgroupname = self.get_targetgroupname()
        self.dedicated_nfs_path = repository_root + '/' + dir_dedicated_nfs + '/' + self.name

        # In case create_vm, domain directory does not exist yet so skip.
        if not os.path.isdir("/%s/%s/%s" % (repository_root, dir_domain, self.name)):
            return

        # set dedicated nfs size
        self.dedicated_nfs_size = self.get_dedicated_nfs_size()

        # set snapshot list
        self.snapshot_list = self.get_snapshot_list()

        # get domain configuration from vm_cfg 
        self.extract_vm_cfg("/%s/%s/%s/vm_cfg.py" % (repository_root, dir_domain, self.name))

    def extract_vm_cfg(self, vm_cfg_path):
        if 'vm_cfg' in sys.modules:
            del(sys.modules['vm_cfg'])
        if not (os.path.isfile(vm_cfg_path)):
            return(1)
        sys.path.append(os.path.dirname(vm_cfg_path))
        import vm_cfg
        #reload(vm_cfg)
        sys.path.remove(os.path.dirname(vm_cfg_path))

        volume_list = []
        if 'disk' in dir(vm_cfg):
            for each_disk in vm_cfg.disk:
                disk = {}
                disk_element_list = each_disk.split(",")
                if disk_element_list[1] == 'hdc:cdrom':
                    continue
                else:
                    disk["backend"] = disk_element_list[0].lstrip("file:").lstrip("phy:")
                    disk["frontend"] = disk_element_list[1]
                    disk["permission"] = disk_element_list[2]
                    volume_list.append(disk)

        network_list = []
        if 'vif' in dir(vm_cfg):
            for each_vif in vm_cfg.vif:
                vif = {}
                vif_element_list = each_vif.split(",")
                #default
                vif["type"] = 'netfront'
                vif["bridge"] = 'xenbr0'
                vif["mac"] = None
                for kv in vif_element_list:
                    kv_list = kv.split("=")
                    key = kv_list[0].strip()
                    value = kv_list[1].strip()
                    if (key == "type"):
                        vif["type"] = value
                    elif (key == "bridge"):
                        vif["bridge"] = value
                    elif (key == "mac"):
                        vif["mac"] = value
                    else:
                        break
                network_list.append(vif)
        self.vm_cfg = vm_cfg
        self.volume_list = volume_list
        self.network_list = network_list

    def generate_new_vm_cfg(self):
        self.new_vm_cfg = []
        self.vm_cfg.name = self.name

        exclude_list = ['__builtins__', '__doc__', '__file__', '__name__', '__package__', 'disk', 'uuid', 'vif']
        prop_list = dir(self.vm_cfg)
        for exclude in exclude_list:
            if exclude in prop_list:
                prop_list.remove(exclude)

        for prop in prop_list:
            if prop == 'ip' and self.ip != 'n/a':
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, self.ip))
            elif prop == 'hostname' and self.hostname != 'n/a':
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, self.hostname))
            elif prop == 'vfb' or prop == 'vif_other_config':
                self.new_vm_cfg.append("%s = %s\n" % (prop, getattr(self.vm_cfg, prop)))
            else:
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, getattr(self.vm_cfg, prop)))

        self.new_vm_cfg.append("disk = [\n")
        for volume in self.volume_list:
            if volume['frontend'] == 'hdc:cdrom':
                continue
            else:
                self.new_vm_cfg.append("'phy:%s,%s,%s',\n" % (volume["backend"], volume["frontend"], volume["permission"]))
        self.new_vm_cfg.append("]\n")

        self.new_vm_cfg.append("vif = [\n")
        for network in self.network_list:
            self.new_vm_cfg.append("'type=%s,bridge=%s,mac=%s',\n" % (network["type"], network["bridge"], network["mac"]))
        self.new_vm_cfg.append("]\n")

    def generate_new_vm_cfg_for_ovm(self):
        self.new_vm_cfg = []
        self.vm_cfg.name = self.name

        exclude_list = ['__builtins__', '__doc__', '__file__', '__name__', '__package__', 'disk', 'uuid', 'vif']
        prop_list = dir(self.vm_cfg)
        for exclude in exclude_list:
            if exclude in prop_list:
                prop_list.remove(exclude)

        for prop in prop_list:
            if prop == 'ip' and self.ip != 'n/a':
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, self.ip))
            elif prop == 'hostname' and self.hostname != 'n/a':
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, self.hostname))
            elif prop == 'vfb' or prop == 'vif_other_config':
                self.new_vm_cfg.append("%s = %s\n" % (prop, getattr(self.vm_cfg, prop)))
            else:
                self.new_vm_cfg.append("%s = '%s'\n" % (prop, getattr(self.vm_cfg, prop)))

        self.new_vm_cfg.append("disk = [\n")
        for volume in self.volume_list:
            if volume['frontend'] == 'hdc:cdrom':
                continue
            else:
                self.new_vm_cfg.append("'phy:%s,%s,%s',\n" % (volume["backend_by_id"], volume["frontend"], volume["permission"]))
        self.new_vm_cfg.append("]\n")

        self.new_vm_cfg.append("vif = [\n")
        for network in self.network_list:
            self.new_vm_cfg.append("'type=%s,bridge=%s,mac=%s',\n" % (network["type"], network["bridge"], network["mac"]))
        self.new_vm_cfg.append("]\n")

    def get_targetname(self):
        name = re.sub('_', '', self.name)
        name = name.lower()
        targetname = "%(iqn_base)s:%(name)s" % {"iqn_base":iqn_base,"name":name}
        return(targetname)

    def get_targetgroupname(self):
        targetgroupname = "%s:%s" % (repository_name, self.name)
        return(targetgroupname)

    def get_backend(self, zfs_ip, targetname, lun):
        backend = "/dev/disk/by-path/ip-%(zfs_ip)s:3260-iscsi-%(targetname)s-lun-%(lun)s" % {"zfs_ip":zfs_ip, "targetname":targetname, "lun":lun}
        return(backend)

    def get_diskname_by_backend(self, backend):
        diskname = re.sub('^.*:', '', backend) 
        diskname = re.sub('-lun-.*$', '', diskname) 
        return(diskname)

    def get_snapshot_list(self):
        cmd = "pfexec /usr/sbin/zfs list -H -r -o name -S creation -t snapshot %s | sed 's/^.*@//g' | uniq" % self.zfs_path
        res = commands.getoutput(cmd)
        return(res.splitlines())

    def set_shareddisk_list(self):
        pass

    def get_latest_snapshot(self, volume):
        cmd = "pfexec /usr/sbin/zfs list -H -r -o name -S creation -t snapshot %s | head -1 | sed 's/^.*@//g'" % volume
        latest_snapshot = commands.getoutput(cmd)
        return(latest_snapshot)

    def get_dedicated_nfs_size(self):
        cmd = "pfexec /usr/sbin/zfs get -H quota %s | awk '{ print $3 }'" % self.dedicated_nfs_path
        dedicated_nfs_size= commands.getoutput(cmd)
        return(dedicated_nfs_size)

class Template(Domain):
    def __init__(self, name):
        self.name = name
        self.type = "template"
        self.zfs_path = "%(repository_root)s/%(dir_template)s/%(name)s" % {"repository_root":repository_root,"dir_template":dir_template,"name":self.name}
        Domain.__init__(self)

class Vm(Domain):
    def __init__(self, name):
        self.name = name
        self.type = "vm"
        self.zfs_path = "%(repository_root)s/%(dir_vm)s/%(name)s" % {"repository_root":repository_root,"dir_vm":dir_vm,"name":self.name}
        Domain.__init__(self)

