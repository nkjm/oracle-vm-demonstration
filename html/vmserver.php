<table style='width:100%;'>
    <th>Name</th>
    <th>CPU</th>
    <th>Memory</th>
<?php
foreach ($ovs_list as $ovs_name => $ovs) {
?>
    <tr>
        <td style='padding:5px;'><?php echo $ovs_name; ?></td>
        <td style='padding:5px;'><?php echo $ovs['cpu_used'] . ' Allocated / ' . $ovs['cpu_total']; ?> Total</td>
        <td style='padding:5px;'><?php echo $ovs['mem_used'] . 'GB  Allocated / ' . $ovs['mem_total']; ?> GB Total</td>
    </tr>
<?php
}
?>
</table>
