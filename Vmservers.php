<?php
class Vmservers {
    public $ovm_user;
    public $ovm_password;
    public $ovm_pool;

    function __construct($ovm_user, $ovm_password, $ovm_pool) {
        $this->ovm_user = $ovm_user;
        $this->ovm_password = $ovm_password;
        $this->ovm_pool = $ovm_pool;
    }

    public function get_ovs_list() {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password svr ls -s $this->ovm_pool | sed '1d' | awk '{ print $2 }' | sed -e 's/$/,/g' | tr -d '\n' | sed 's/,$//g'";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg("Failed to fetch VM Server list.");
            return(ERROR);
        }
        foreach($array_output as $ovs_name) {
            $ovs_detail = self::get_ovs_detail($ovs_name);
            $ovs_list[$ovs_name]= $ovs_detail;
        }
        if (is_array($ovs_list)) {
            ksort($ovs_list);
        }
        return($ovs_list);
    }

    public function get_ovs_detail($ovs_name) {
        global $error;

        $cmd = "ssh localhost ovm -u $this->ovm_user -p $this->ovm_password svr info -n $ovs_name -s $this->ovm_pool | grep -e 'Server Host/IP' -e 'Number of CPUs' -e 'Allocated VCPUs' -e 'Memory' -e 'Status' | sed -e 's/  *//g' | sed -e 's/(MB)//g'";
        exec($cmd, $array_output, $return);
        if ($return != 0) {
            $error->set_msg("Failed to fetch VM Server detail.");
            return(ERROR);
        }
        foreach ($array_output as $line) {
            $array_element = explode(':', $line);
            if ($array_element[0] == 'NumberofCPUs') {
                $ovs_detail['cpu_total'] = $array_element[1];
            } elseif ($array_element[0] == 'AllocatedVCPUs') {
                $ovs_detail['cpu_used'] = $array_element[1];
            } elseif ($array_element[0] == 'TotalMemory') {
                $ovs_detail['mem_total'] = round($array_element[1] / 1024);
            } elseif ($array_element[0] == 'FreeMemory') {
                $ovs_detail['mem_free'] = round($array_element[1] / 1024);
            } elseif ($array_element[0] == 'ServerHost/IP') {
                $ovs_detail['ip'] = $array_element[1];
            } elseif ($array_element[0] == 'Status') {
                $ovs_detail['status'] = $array_element[1];
            }
        }
        $ovs_detail['mem_used'] = $ovs_detail['mem_total'] - $ovs_detail['mem_free'];
        return($ovs_detail);
    }

}
?>
