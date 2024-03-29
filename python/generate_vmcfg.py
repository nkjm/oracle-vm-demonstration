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

volume_list = []  
network_list = []

        # In case create_vm, domain directory does not exist yet so skip.
        if not os.path.isdir("/%s/%s/%s" % (repository_root, dir_domain, self.name)):
            return

        # get domain configuration from vm_cfg 
        self.extract_vm_cfg("/%s/%s/%s/vm_cfg.py" % (repository_root, dir_domain, self.name))

    def extract_vm_cfg(self, vm_cfg_path):
        if 'vm_cfg' in sys.modules:
            del(sys.modules['vm_cfg'])
        if not (os.path.isfile(vm_cfg_path)):
            print "Specified domain configuration file does not exist. Exiting... "
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
            self.new_vm_cfg.append("%s = '%s'\n" % (prop, getattr(self.vm_cfg, prop)))

        self.new_vm_cfg.append("disk = [\n")
        for volume in self.volume_list:
            self.new_vm_cfg.append("'phy:%s,%s,%s',\n" % (volume["backend"], volume["frontend"], volume["permission"]))
        self.new_vm_cfg.append("]\n")

        self.new_vm_cfg.append("vif = [\n")
        for network in self.network_list:
            self.new_vm_cfg.append("'type=%s,bridge=%s,mac=%s',\n" % (network["type"], network["bridge"], network["mac"]))
        self.new_vm_cfg.append("]\n")


