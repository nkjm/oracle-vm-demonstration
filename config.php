<?php

/***
**** Essential Configuration
***/

// ZFS Storage Server Login Information
define("ZFS_HOSTNAME", '');
define("ZFS_USER", '');
define("ZFS_ZPOOL", '');
define("ZFS_REPOSITORY_NAME", '');
define("ZFS_DEDICATED_NFS_PATH", 'rpool/virtzfs/' . ZFS_REPOSITORY_NAME . '/nfs/dedicated');

// OVM Information
define("OVM_USER", '');
define("OVM_PASSWORD", '');
define("OVM_POOL", '');

// Error
define("ERROR", "ERROR");
