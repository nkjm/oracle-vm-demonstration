<?php
class Guests {
    public $ovm_user;
    public $ovm_password;
    public $ovm_pool;
    public $zfs_user;
    public $zfs_hostname;

    function __construct($ovm_user, $ovm_password, $ovm_pool, $zfs_user = 'n/a', $zfs_hostname = 'n/a') {
        $this->ovm_user = $ovm_user;
        $this->ovm_password = $ovm_password;
        $this->ovm_pool = $ovm_pool;
        $this->zfs_user = $zfs_user;
        $this->zfs_hostname = $zfs_hostname;
    }

    public function switch_power($vm_name, $vm_power) {
        global $error;

        if ($vm_power == 'off') {
            $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm poweron -n $vm_name -s $this->ovm_pool";
        } elseif ($vm_power == 'on') {
            $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm poweroff -n $vm_name -s $this->ovm_pool";
        } else {
            $error->set_msg("VM Status is not ready for Power ON/OFF.");
            return(ERROR);
        }
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg("Failed switch power.");
            return(ERROR);
        }
    }

    public function get_vm_list($ovs_list) {
        global $error;

        // Get VM List from VM Manager
        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm ls -l -s $this->ovm_pool | sed '1d' | sed -e 's/  */,/g' | sed -e 's/,$//g'";
        exec($cmd, $array_output_from_ovm, $return);
        if ($return != 0) {
            $error->set_msg("Failed fetch VM list from VM Manager.");
            return(ERROR);
        }

        // Get VM List from ZFS
        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs list vm";
        exec($cmd, $array_output_from_zfs, $return);
        if ($return != 0) {
            $error->set_msg('Failed to fetch VM list from ZFS.');
            return(ERROR);
        }
        $vm_list_zfs = array();
        foreach($array_output_from_zfs as $line) {
            $array_vm_element_zfs = explode(":", $line);
            $vm_list_zfs[$array_vm_element_zfs[0]]['ip'] = $array_vm_element_zfs[1];
            $vm_list_zfs[$array_vm_element_zfs[0]]['dedicated_nfs_size'] = $array_vm_element_zfs[2];
            $vm_list_zfs[$array_vm_element_zfs[0]]['snapshot_list'] = explode(',', $array_vm_element_zfs[3]);
        }

        $vm_list = array();
        foreach($array_output_from_ovm as $k => $line) {
            $array_vm_element_ovm = explode(',', $line);
            $vm_list[$array_vm_element_ovm[0]]['memory'] = $array_vm_element_ovm[2];
            $vm_list[$array_vm_element_ovm[0]]['cpu'] = $array_vm_element_ovm[3];
            $vm_list[$array_vm_element_ovm[0]]['ip'] = $vm_list_zfs[$array_vm_element_ovm[0]]['ip'];
            $vm_list[$array_vm_element_ovm[0]]['dedicated_nfs_size'] = $vm_list_zfs[$array_vm_element_ovm[0]]['dedicated_nfs_size'];
            $vm_list[$array_vm_element_ovm[0]]['snapshot_list'] = $vm_list_zfs[$array_vm_element_ovm[0]]['snapshot_list'];
            if ($array_vm_element_ovm[4] == 'Running') {
                $vm_list[$array_vm_element_ovm[0]]['power'] = 'on';
                $vm_list[$array_vm_element_ovm[0]]['power_next'] = 'off';
                $vm_list[$array_vm_element_ovm[0]]['vmserver'] = $array_vm_element_ovm[5];
                $vm_list[$array_vm_element_ovm[0]]['vnc'] = self::get_vnc($array_vm_element_ovm[0], $ovs_list[$array_vm_element_ovm[5]]['ip']);
            } elseif ($array_vm_element_ovm[4] == 'Powered') {
                $vm_list[$array_vm_element_ovm[0]]['power'] = 'off';
                $vm_list[$array_vm_element_ovm[0]]['power_next'] = 'on';
                $vm_list[$array_vm_element_ovm[0]]['vmserver'] = 'n/a';
                $vm_list[$array_vm_element_ovm[0]]['vnc'] = 'n/a';
            } else {
                $vm_list[$array_vm_element_ovm[0]]['power'] = 'intermediate';
                $vm_list[$array_vm_element_ovm[0]]['power_next'] = 'n/a';
                $vm_list[$array_vm_element_ovm[0]]['vmserver'] = 'n/a';
                $vm_list[$array_vm_element_ovm[0]]['vnc'] = 'n/a';
            }
        }
        if (is_array($vm_list)) {
            ksort($vm_list);
        }
        return($vm_list);
    }

    public function get_vm_power($vm_name) {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm stat -n $vm_name -s $this->ovm_pool";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg("Failed to fetch VM power status.");
            return(ERROR);
        }
        if ($array_output[0] == 'Running') {
            $vm_power = 'on';
        } elseif ($array_output[0] == 'Powered Off') {
            $vm_power = 'off';
        } else {
            $vm_power = 'intermediate';
        }
        return($vm_power);
    }
    public function import_vm($vm_name, $vm_password, $vm_os) {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password img reg -n $vm_name -c $vm_password -o \'$vm_os\' -s $this->ovm_pool";
        exec($cmd, $array_output, $return);
        if ($array_output[0] != "Registering virtual machine image. Please check the status.") {
            $error->set_msg("Failed to register VM image to VM Manager.");
            return(ERROR);
        }
        $timeout = 20;
        $elapsed_time = 0;
        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password img stat -n $vm_name -s $this->ovm_pool";
        while (TRUE) {
            $array_output = '';
            exec($cmd, $array_output, $return);
            if (trim($array_output[0]) == 'Pending') {
                break;
            }
            if ($elapsed_time > $timeout) {
                $error->set_msg("Had been waiting for VM Status becoming 'Pending' but exceeded " . $timeout . " seconds. VM Status: '" . trim($array_output[0]) . "'");
                return(ERROR);
            }
            $elapsed_time += 1;
            sleep(1);
        }
        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password img approve -n $vm_name -s $this->ovm_pool";
        $array_output = '';
        exec($cmd, $array_output, $return);
        if ($array_output[0] != 'Virtual machine image "' . $vm_name . '" approved.') {
            $error->set_msg("Failed to approve registered VM image to VM Manager.");
            return(ERROR);
        }
    }

    public function delete_vm($vm_name, $vm_power) {
        global $error;

        if ($vm_power != 'off') {
            $error->set_msg("Virtual Machine must be Power OFF first to delete.");
            return(ERROR);
        }
        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm del -n $vm_name -s $this->ovm_pool";
        exec($cmd, $array_output, $return);
        if ($array_output[0] != 'Deleted virtual machine "' . $vm_name . '" and its image files.') {
            $error->set_msg("Failed to delete VM from VM Manager.");
            return(ERROR);
        }
    }

    public function update_cpu($vm_name, $cpu) {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm conf -n $vm_name -s $this->ovm_pool -c $cpu";
        exec($cmd, $array_output, $return);
        if ($array_output[0] != 'Number of VCPUs changed to "' . $cpu . '".') {
            $error->set_msg("Failed to change the number of CPU.");
            return(ERROR);
        }
    }

    public function update_memory($vm_name, $memory) {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password vm conf -n $vm_name -s $this->ovm_pool -m $memory";
        exec($cmd, $array_output, $return);
        if ($array_output[0] != 'Memory size changed to "' . $memory . '".') {
            $error->set_msg("Failed to change the size of Memory.");
            return(ERROR);
        }
    }

    public function get_vnc($vm_name, $vmserver_ip) {
        global $error;

        if ($vmserver_ip == 'n/a') {
            $error->set_msg("VNC has not been assigned yet cause VM status is not running.");
            return(ERROR);
        }
        $cmd = "ssh -l root " . $vmserver_ip . " xm list $vm_name --long | grep 'location 0.0.0.0' | cut -d':' -f2 | sed -e 's/)//g'";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg("Failed to fetch VNC Port from VM Server.");
            return(ERROR);
        }
        if (array_key_exists(0, $array_output)) {
            $vnc = $vmserver_ip . ':' . $array_output[0];
        } else {
            $vnc = 'Not Assigned.';
        }
        return($vnc);
    }
}
?>
