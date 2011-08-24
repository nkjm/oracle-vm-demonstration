<?php
class Zfs {
    public $zfs_user; 
    public $zfs_hostname; 
 
    function __construct($zfs_user, $zfs_hostname) { 
        $this->zfs_user = $zfs_user; 
        $this->zfs_hostname = $zfs_hostname; 
    } 

    public function create_vm($vm_name, $template_name, $vm_ip) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs create vm -n $vm_name -t $template_name -a $vm_ip -i 1";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
    }

    public function delete_vm($vm_name) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs delete vm -v $vm_name";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
    }

    public function snapshot_vm($vm_name, $snapshot_name) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs snapshot vm -v $vm_name -n $snapshot_name";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
    }

    public function rollback_vm($vm_name, $snapshot_name, $vm_power) {
        global $error;

        if ($vm_power != 'off') {
            $error->set_msg("Virtual Machine must be Power OFF first to rollback.");
            return(ERROR);
        }
        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs rollback vm -v $vm_name -s $snapshot_name";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
    }

    public function get_template_list() {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs list template";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
        return($array_output);
    }

    public function get_snapshot_list($vm_name) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname virtzfs list snapshot -v $vm_name";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
        return($array_output);
    }

    public function get_dedicated_nfs($vm_name) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname zfs get -H quota " . ZFS_DEDICATED_NFS_PATH . "/$vm_name | awk '{ print $3 }'";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
        return($array_output[0]);
    }

    public function update_dedicated_nfs_size($vm_name, $size) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname zfs set quota=" . $size . "G " . ZFS_DEDICATED_NFS_PATH . "/$vm_name";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
    }

    public function get_zpool_detail($zfs_zpool) {
        global $error;

        $cmd = "ssh -l $this->zfs_user $this->zfs_hostname zpool list -H $zfs_zpool | awk '{ print $2\",\"$5 }'";
        exec($cmd, $array_output, $return);
        $array_zpool = explode(",", $array_output[0]);
        if ($return != 0) {
            $error->set_msg($array_output[0]);
            return(ERROR);
        }
        $zpool['space_total'] = $array_zpool[0];
        $zpool['space_usage'] = $array_zpool[1];
        return($zpool);
    }
}
?>
