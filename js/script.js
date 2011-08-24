$(function(){
    // Open New VM Form
    $('#new_vm .new').click(function(){
        $('#new_vm_form').slideToggle();
    });

    // Close New VM Form
    $('#new_vm_form .button_no').click(function(){
        $('#new_vm_form').slideUp('slow');
    });

    // vm_create
    $('#new_vm_form .button_yes').click(function(){
        var $template_name = $('#new_vm_form input:radio:checked').attr('value');
        var $vm_name = $('#new_vm_form input[name="vm_name"]').attr('value');
        var $vm_password = $('#new_vm_form input[name="vm_password"]').attr('value');
        var $vm_ip = $('#new_vm_form input[name="vm_ip"]').attr('value');
        $('#new_vm_form').slideUp('slow');
        $.confirm.status();
        $.post(
            '/op.php', 
            { op: 'vm_create', template_name: $template_name, vm_name: $vm_name, vm_password: $vm_password, vm_ip: $vm_ip },
            function(data){
                if (data.error == 1) {
                    $message = '';
                    for (i in data.stack_msg) {
                        $message += data.stack_msg[i] + '<br/>';
                    }
    	            $.confirm.error({
                        'title' : 'Error',
                        'message' : $message,
                        'buttons'	: {
                            'Close'	: {
                                'class'	: 'gray',
                                'action':   function(){}
                            }
                        }
    	            });
                } else {
                    $('#new_vm_form input[name="vm_name"').attr('value', '');
                    $('#new_vm_form input[name="vm_password"]').attr('value', '');
                    $('#new_vm_form input[name="vm_ip"').attr('value', '');
                    $('#middle').load('/ #middle', function(){
                        $.getScript('/js/script.js');
                        $.confirm.hide(); 
                    });
                }
            },'json'
        );
    });

    // vm_switch_power
    $('#existing_vm .vm .vm_switch_power').click(function(){
        var $vm_name = $(this).attr('vm_name');
        var $vm_power = $(this).attr('vm_power');
        var $elem = $(this).closest('.vm');
    	
    	$.confirm({
            'title'     :   'Confirmation',
            'message'   :   'Going to Power ' +$vm_power+ ' Virtual Machine: "' +$vm_name+ '".<br />Are you sure?',
            'buttons'   :   {
                'Yes'   :   {
                    'class' :   'blue',
                    'action':   function(){
                                    $.post(
                                        '/op.php', 
                                        { op: "vm_switch_power", vm_name: $vm_name },
                                        function(data){
                                            if (data.error == 1) {
                                                $message = '';
                                                for (i in data.stack_msg) {
                                                    $message += data.stack_msg[i] + '<br/>';
                                                }
    	                                        $.confirm.error({
                                                    'title' : 'Error',
                                                    'message' : $message,
                                                    'buttons'	: {
                                                        'Close'	: {
                                                            'class'	: 'gray',
                                                            'action':   function(){}
                                                        }
                                                    }
    	                                        });
                                            } else {
                                                $('#middle').load('/ #middle', function(){
                                                    $.getScript('/js/script.js');
                                                    $.confirm.hide(); 
                                                });
                                            }
                                        },'json'
                                    );
                                }
                },
                'No'	: {
                    'class'	: 'gray',
                    'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
                }
            }
    	});
    });

    // Display Login Information
    $('#existing_vm .vm_name').click(function(){
        if ($(this).attr('vnc') == 'n/a') {
            var $vnc = 'Not Assigned.';
        } else {
            var $vnc = $(this).attr('vnc');
        }
        $dedicated_nfs_path = $(this).attr('dedicated_nfs_path');
        $ip = $(this).attr('ip');
    	$.confirm({
            'title' : 'Login Information',
            'message' : 'IP Address : <span style="color:#111;">' +$ip+ '</span><br/>VNC Console : <span style="color:#111;">' +$vnc+ '</span><br/>Dedicated NFS : <span style="color:#111;">' +$dedicated_nfs_path+ '</span>',
            'buttons' : {
                'Close'	: {
                    'class' : 'gray',
                    'action' : function(){}
                }
            }
    	});
    });

    // Display VM CPU Form
    $('#existing_vm .vm_cpu').click(function(){
        $('.current_value', $(this)).remove();
        $(':hidden', $(this)).show();
    });

    // vm_update_cpu
    $('#existing_vm .vm_cpu .button_yes').click(function(){
        var $vm_name = $(':hidden', $(this).parent()).attr('value');
        var $vm_cpu = $(':text', $(this).parent()).attr('value');
        $.confirm.status();
        $.post(
            '/op.php', 
            { op: 'vm_update_cpu', vm_name: $vm_name, vm_cpu: $vm_cpu },
            function(data){
                if (data.error == 1) {
                    $message = '';
                    for (i in data.stack_msg) {
                        $message += data.stack_msg[i] + '<br/>';
                    }
    	            $.confirm.error({
                        'title' : 'Error',
                        'message' : $message,
                        'buttons'	: {
                            'Close'	: {
                                'class'	: 'gray',
                                'action':   function(){}
                            }
                        }
    	            });
                } else {
                    $('#middle').load('/ #middle', function(){
                        $.getScript('/js/script.js');
                        $.confirm.hide(); 
                    });
                }
            },'json'
        );
    });

    // Display VM Memory Form
    $('#existing_vm .vm_memory').click(function(){
        $('.current_value', $(this)).remove();
        $(':hidden', $(this)).show();
    });

    // vm_update_memory
    $('#existing_vm .vm_memory .button_yes').click(function(){
        var $vm_name = $(':hidden', $(this).parent()).attr('value');
        var $vm_memory = $(':text', $(this).parent()).attr('value');
        $.confirm.status();
        $.post(
            '/op.php', 
            { op: 'vm_update_memory', vm_name: $vm_name, vm_memory: $vm_memory },
            function(data){
                if (data.error == 1) {
                    $message = '';
                    for (i in data.stack_msg) {
                        $message += data.stack_msg[i] + '<br/>';
                    }
    	            $.confirm.error({
                        'title' : 'Error',
                        'message' : $message,
                        'buttons'	: {
                            'Close'	: {
                                'class'	: 'gray',
                                'action':   function(){}
                            }
                        }
    	            });
                } else {
                    $('#middle').load('/ #middle', function(){
                        $.getScript('/js/script.js');
                        $.confirm.hide(); 
                    });
                }
            },'json'
        );
    });

    // Display VM Dedicated NFS Form
    $('#existing_vm .vm_dedicated_nfs_size').click(function(){
        $('.current_value', $(this)).remove();
        $(':hidden', $(this)).show();
    });

    // vm_update_dedicated_nfs_size
    $('#existing_vm .vm_dedicated_nfs_size .button_yes').click(function(){
        var $vm_name = $(':hidden', $(this).parent()).attr('value');
        var $vm_dedicated_nfs_size = $(':text', $(this).parent()).attr('value');
        $.confirm.status();
        $.post(
            '/op.php', 
            { op: 'vm_update_dedicated_nfs_size', vm_name: $vm_name, vm_dedicated_nfs_size: $vm_dedicated_nfs_size },
            function(data){
                if (data.error == 1) {
                    $message = '';
                    for (i in data.stack_msg) {
                        $message += data.stack_msg[i] + '<br/>';
                    }
    	            $.confirm.error({
                        'title' : 'Error',
                        'message' : $message,
                        'buttons'	: {
                            'Close'	: {
                                'class'	: 'gray',
                                'action':   function(){}
                            }
                        }
    	            });
                } else {
                    $('#middle').load('/ #middle', function(){
                        $.getScript('/js/script.js');
                        $.confirm.hide(); 
                    });
                }
            },'json'
        );
    });

    // vm_snapshot
    $('#existing_vm .vm .vm_snapshots input[value="Backup"]').click(function(){
        var $vm_name = $(this).attr('vm_name');
    	
    	$.confirm({
            'title'     :   'Confirmation',
            'message'   :   'Going to take snaphost of Virtual Machine: "' +$vm_name+ '".<br />Are you sure?',
            'buttons'   :   {
                'Yes'   :   {
                    'class' :   'blue',
                    'action':   function(){
                                    $.post(
                                        '/op.php', 
                                        { op: "vm_snapshot", vm_name: $vm_name },
                                        function(data){
                                            if (data.error == 1) {
                                                $message = '';
                                                for (i in data.stack_msg) {
                                                    $message += data.stack_msg[i] + '<br/>';
                                                }
    	                                        $.confirm.error({
                                                    'title' : 'Error',
                                                    'message' : $message,
                                                    'buttons'	: {
                                                        'Close'	: {
                                                            'class'	: 'gray',
                                                            'action':   function(){}
                                                        }
                                                    }
    	                                        });
                                            } else {
                                                $('#middle').load('/ #middle', function(){
                                                    $.getScript('/js/script.js');
                                                    $.confirm.hide(); 
                                                });
                                            }
                                        },'json'
                                    );
                                }
                },
                'No'	: {
                    'class'	: 'gray',
                    'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
                }
            }
    	});
    });

    // vm_rollback
    $('#existing_vm .vm .vm_snapshots input[value="Rollback"]').click(function(){
        var $vm_name = $(this).attr('vm_name');
        var $vm_snapshot_name = $('select option:selected', $(this).parent()).attr('value');;
    	
    	$.confirm({
            'title'     :   'Confirmation',
            'message'   :   'Going to rollback Virtual Machine: "' +$vm_name+ '" to Snapshot: "' +$vm_snapshot_name+ '".<br />Are you sure?',
            'buttons'   :   {
                'Yes'   :   {
                    'class' :   'blue',
                    'action':   function(){
                                    $.post(
                                        '/op.php', 
                                        { op: "vm_rollback", vm_name: $vm_name, vm_snapshot_name: $vm_snapshot_name },
                                        function(data){
                                            if (data.error == 1) {
                                                $message = '';
                                                for (i in data.stack_msg) {
                                                    $message += data.stack_msg[i] + '<br/>';
                                                }
    	                                        $.confirm.error({
                                                    'title' : 'Error',
                                                    'message' : $message,
                                                    'buttons'	: {
                                                        'Close'	: {
                                                            'class'	: 'gray',
                                                            'action':   function(){}
                                                        }
                                                    }
    	                                        });
                                            } else {
                                                $('#middle').load('/ #middle', function(){
                                                    $.getScript('/js/script.js');
                                                    $.confirm.hide(); 
                                                });
                                            }
                                        },'json'
                                    );
                                }
                },
                'No'	: {
                    'class'	: 'gray',
                    'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
                }
            }
    	});
    });

    // vm_delete
    $('#existing_vm .vm_delete').click(function(){
        var $vm_name = $(this).attr('vm_name');
        var $elem = $(this).closest('.vm');
    	
    	$.confirm({
            'title'     :   'Confirmation',
            'message'   :   'Going to delete Virtual Machine: "' +$vm_name+ '".<br />Are you sure?',
            'buttons'   :   {
                'Yes'   :   {
                    'class' :   'blue',
                    'action':   function(){
                                    $.post(
                                        '/op.php', 
                                        { op: "vm_delete", vm_name: $vm_name },
                                        function(data){
                                            if (data.error == 1) {
                                                $message = '';
                                                for (i in data.stack_msg) {
                                                    $message += data.stack_msg[i] + '<br/>';
                                                }
    	                                        $.confirm.error({
                                                    'title' : 'Error',
                                                    'message' : $message,
                                                    'buttons'	: {
                                                        'Close'	: {
                                                            'class'	: 'gray',
                                                            'action':   function(){}
                                                        }
                                                    }
    	                                        });
                                            } else {
                                                $.confirm.hide(); 
                                                $elem.fadeOut(2000);
                                            }
                                        },'json'
                                    );
                                }
                },
                'No'	: {
                    'class'	: 'gray',
                    'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
                }
            }
    	});
    });
});
