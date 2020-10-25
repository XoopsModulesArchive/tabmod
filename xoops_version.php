<?php

$modversion['name'] = _MI_TABMOD_NAME;
$modversion['version'] = 1;
$modversion['description'] = _MI_TABMOD_DESC;
$modversion['credits'] = '';
$modversion['author'] = 'Khimyack Bogdan';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 1;
$modversion['image'] = 'images/slogo.png';
$modversion['dirname'] = 'tabmod';

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'tm_tabs';
#$modversion['tables'][1] = "tm_links";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Main contents
//$modversion['hasMain'] = 1;
