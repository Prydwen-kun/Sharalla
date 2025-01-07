<?php
#APP TAG
define('APP_TAG', 'Sharalla');

#USER POWER
define('ADMIN', 100);
define('MODERATOR', 50);
define('USER', 20);
define('BANNED_USER', 15);
define('INVITE', 10);

#ONLINE STATUS
define('STATUS_ONLINE', 1);
define('STATUS_OFFLINE', 2);
define('STATUS_AWAY', 3);
define('STATUS_NOTDISTURB', 4);

#USER RANK
define('R_ADMIN', 1);
define('R_MOD', 2);
define('R_USER', 3);
define('R_BAN', 4);
define('R_GUEST', 5);

#DATABASE
define('DB_ENGINE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sharalla');
define('DB_CHARSET', 'utf8mb4');
define('DB_USER', 'root');
define('DB_PWD', '');

#ADMIN FALLBACK NAME
define('ADMIN_NAME', 'admin');

#FUNCTION RETURN CODE[plage 0-99]
//signup error
define('PWD_CONFIRM_ERROR', 10);
define('USERNAME_LENGTH_ERROR', 11);
define('PWD_LENGTH_ERROR', 12);
define('EMAIL_ERROR', 13);
define('USERNAME_TAKEN_ERROR', 15);
define('POST_EMPTY', 30);

//login error
define('CREDENTIALS_ERROR', 16);
//request error
define('REQ_ERROR', 35);
define('REQ_PREP_ERROR', 36);
//function OK
define('RETURN_OK', 201);

//HTTPS config
define('HTTPS_FLAG', false);
define('HTTP_PROTO', 'http://');
define('DOMAIN_NAME', $_SERVER['HTTP_HOST']);
define('ORIGIN_DOMAIN', 'http://localhost:5173');

//DIR CONST
#for files upload
#target directory
define('UPLOAD_DIR', 'files/');
define('_2GB_UPLOAD_LIMIT', 2000000000);

//FILES FUNCTION RETURN CODE[plage 100-200]
define('FILE_SIZE_ERROR', 100);
define('FILE_EXT_ERROR', 101);
define('FILE_TITLE_SIZE_ERR', 102);
define('FILE_DESC_SIZE_ERR', 103);
define('FILE_UPLOAD_ERROR', 147);
define('FILE_OK', 199);

//COMMENT MODEL RETURN CODE[plage 300-400]
define('COMMENT_EMPTY', 301);
