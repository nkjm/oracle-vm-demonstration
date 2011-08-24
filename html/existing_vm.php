<table style='margin: 0px auto 0px auto; width: 90%;'>
    <tr>
        <th style='border-color:#ffffff;'>&nbsp</th>
        <th>VM Name</th>
        <th>CPU</th>
        <th>Memory</th>
        <th>Dedicated NFS</th>
        <th>Snapshots</th>
        <th style='border-color:#ffffff;'>&nbsp</th>
    </tr>
<?php
if (is_array($vm_list)) {
    foreach ($vm_list as $vm_name => $vm) {
        echo "<tr class='vm'>\n";
    
        // Power
        echo "<td style='border-color:#ffffff;'>\n";
        echo "<img class='vm_switch_power' src='/img/vm_power_" . $vm['power'] . ".png' vm_name='" . $vm_name . "' vm_power='" . strtoupper($vm['power_next']) . "'></img>\n";
        echo "</td>\n";
    
        // VM Name
        echo "<td class='vm_name' style='cursor:pointer;' vm_name='" . $vm_name . "' ip='" . $vm['ip'] . "' vnc='" . $vm['vnc'] . "' dedicated_nfs_path='" . ZFS_HOSTNAME . ":" . ZFS_DEDICATED_NFS_PATH . "/" . $vm_name . "'>\n";
        echo $vm_name;
        echo "</td>\n";
    
        // CPU
        echo "<td class='vm_cpu'>\n";
        echo "<span class='current_value'>" . $vm['cpu'] . "</span>\n";
        echo "<div style='display:none;'>\n";
        echo "<input type=text name=vm_cpu size=2 value='" . $vm['cpu'] . "'></input>\n";
        echo "<input type=hidden name=vm_name value='" . $vm_name . "'></input>\n";
        echo "<input type=submit class='button_yes' value='Save'></input>\n";
        echo "</div>\n";
        echo "</td>\n";
    
        // Memory
        echo "<td class='vm_memory'>\n";
        echo "<span class='current_value'>" . $vm['memory'] . " MB</span>\n";
        echo "<div style='display:none;'>\n";
        echo "<input type=text name=vm_memory size=4 value='" . $vm['memory'] . "'></input> MB\n";
        echo "<input type=hidden name=vm_name value='" . $vm_name . "'></input>\n";
        echo "<input type=submit class='button_yes' value='Save'></input>\n";
        echo "</div>\n";
        echo "</td>\n";
    
        // Dedicated NFS
        echo "<td class='vm_dedicated_nfs_size'>\n";
        echo "<span class='current_value'>" . str_replace('G','',$vm['dedicated_nfs_size']) . " GB</span>\n";
        echo "<div style='display:none;'>\n";
        echo "<input type=text name=vm_dedicated_nfs_size size=4 value='" . str_replace('G','',$vm['dedicated_nfs_size']) . "'></input> GB\n";
        echo "<input type=hidden name=vm_name value='" . $vm_name . "'></input>\n";
        echo "<input type=submit class='button_yes' value='Save'></input>\n";
        echo "</div>\n";
        echo "</td>\n";

        // Snapshots
        echo "<td class='vm_snapshots'>\n";
        echo "<input type='submit' class='button_yes' value='Backup' vm_name='" . $vm_name . "'></input>\n";
        echo "<select name=vm_snapshot>\n";
        foreach ($vm['snapshot_list'] as $vm_snapshot) {
            echo "<option value='$vm_snapshot'>$vm_snapshot</option>\n";
        }
        echo "</select>\n";
        echo "<input type='submit' class='button_yes' value='Rollback' vm_name='" . $vm_name . "'></input>\n";
        echo "</td>\n";
    
        // Status and Delete
        echo "<td style='border-color:#ffffff;'>\n";
        echo "<img class='vm_delete' src='/img/delete.png' vm_name='" . $vm_name . "'></img>\n";
        echo "</td>\n";
    
        echo "</tr>\n";
    }
}
?>
</table>
