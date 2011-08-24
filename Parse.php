<?php
class Parse {
    public function vm_name($input) {
        global $error;
        $pattern = '/^[\w_-]+$/';
        $trimmed_input = trim($input);
        if (!preg_match($pattern, $trimmed_input)) {
            $error->set_msg("Invalid VM Name. Available letters are [a-zA-Z] and '_'.");
            return(ERROR);
        }
        return($trimmed_input);
    }

    public function template_name($input) {
        global $error;
        $pattern = '/^[\w_-]+$/';
        $trimmed_input = trim($input);
        if (!preg_match($pattern, $trimmed_input)) {
            $error->set_msg("Invalid Template Name. Available letters are [a-zA-Z] and '_'.");
            return(ERROR);
        }
        return($trimmed_input);
    }

    public function vm_password($input) {
        global $error;
        $pattern = '/^[\w_-]+$/';
        $trimmed_input = trim($input);
        if (!preg_match($pattern, $trimmed_input)) {
            $error->set_msg("Invalid Password. Available letters are [a-zA-Z] and '_'.");
            return(ERROR);
        }
        return($trimmed_input);
    }

    public function vm_ip($input) {
        global $error;
        $pattern = '/^[\d.]+$/';
        $trimmed_input = trim($input);
        if (!preg_match($pattern, $trimmed_input)) {
            $error->set_msg("Invalid IP Address.");
            return(ERROR);
        }
        return($trimmed_input);
    }

    public function vm_cpu($input) {
        global $error;
        if (!is_numeric($input)) {
            $error->set_msg("Invalid Number of CPU. Available value is Number.");
            return;
        }
        $limit_low = 1;
        if ($input < $limit_low) {
            $error->set_msg("Invalid Number of CPU. Available value is more than or equal to $limit_low.");
            return(ERROR);
        }
        $limit_high = 32;
        if ($input > $limit_high) {
            $error->set_msg("Invalid Number of CPU. Available value is less than or equal to $limit_high.");
            return(ERROR);
        }
        return($input);
    }

    public function vm_memory($input) {
        global $error;
        if (!is_numeric($input)) {
            $error->set_msg("Invalid Memory Size. Available value is Number.");
            return;
        }
        $limit_low = 256;
        if ($input < $limit_low) {
            $error->set_msg("Invalid Memory Size. Available value is more than or equal to $limit_low.");
            return(ERROR);
        }
        $limit_high = 4096;
        if ($input > $limit_high) {
            $error->set_msg("Invalide Memory Size. Available value is less than or equal to $limit_high.");
            return(ERROR);
        }
        return($input);
    }

    public function vm_dedicated_nfs_size($input) {
        global $error;
        if (!is_numeric($input)) {
            $error->set_msg("Invalid Dedicated NFS Size. Available value is Number.");
            return;
        }
        $limit_low = 1;
        if ($input < $limit_low) {
            $error->set_msg("Invalid Dedicated NFS Size. Available value is more than or equal to $limit_low.");
            return(ERROR);
        }
        $limit_high = 50;
        if ($input > $limit_high) {
            $error->set_msg("Invalid Dedicated NFS Size. Available value is less than or equal to $limit_high.");
            return(ERROR);
        }
        return($input);
    }

    public function vm_snapshot_name($input) {
        global $error;
        $pattern = '/^\d\d\d\d_\d\d_\d\d_\d\d_\d\d_\d\d$/';
        $trimmed_input = trim($input);
        if (!preg_match($pattern, $trimmed_input)) {
            $error->set_msg("Invalid Snapshot Name. String should be like '0000_00_00_00_00_00'.");
            return(ERROR);
        }
        return($trimmed_input);
    }
}
?>
