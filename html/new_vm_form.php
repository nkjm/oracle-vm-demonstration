<table>
    <tr>
        <td nowrap=''>
            OS
        </td>
        <td nowrap='' style='text-align: left; line-height: 1.4em;'>
            <?php
            /*
            foreach ($template_list as $template) {
                echo "<input type=radio name=template_name value='$template'> " . str_replace('_', ' ', $template) . "</input><br/>\n";
            }
            */
            ?>
            <input type=radio name=template_name checked value='Oracle_Linux_5'> Oracle Linux 5</input><br/>
        </td>
    </tr>
    <tr>
        <td nowrap=''>
            VM Name
        </td>
        <td style='text-align: left;'>
            <input type=text name=vm_name></input>
        </td>
    </tr>
    <tr>
        <td nowrap=''>
            Password
        </td>
        <td style='text-align: left;'>
            <input type=password name=vm_password></input>
        </td>
    </tr>
    <tr>
        <td nowrap=''>
            IP Address
        </td>
        <td style='text-align: left;'>
            <input type=ip name=vm_ip></input>
        </td>
    </tr>
    <tr>
        <td colspan=2 style='font-size: 0.7em; text-align: left;'>
            * Available Network is 10.185.144.0/255.255.248.0.<br/>
            * This IP Address need to be owned by you in advance.
        </td>
    </tr>
</table>
<div style='text-align:center; padding: 10px;'>
    <input type=submit class=button_yes value='CREATE'></input>
    <input type=submit class=button_no value='CANCEL'></input>
</div>
