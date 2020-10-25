<?php

require dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

if (file_exists('../language/' . $xoopsConfig['language'] . '/main.php')) {
    include '../language/' . $xoopsConfig['language'] . '/main.php';
} else {
    include '../language/english/main.php';
}

include '../../../class/xoopstree.php';
#$op = "add";

if (!empty($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        $$k = $v;
    }
}

xoops_cp_header();
echo '<div align="center">';
if (('add_tab' == $op) || !$op) {
    echo '<b>';
}
echo '<a href=index.php?op=add_tab>' . 'Add tab' . '</a>';
if (('add_tab' == $op) || !$op) {
    echo '</b>';
}
echo "<HR></div>\n";

if ('do_add_tab' == $op) {
    $quer = sprintf("insert into %s  (name, parent_id,link,activ_cond) VALUES ('$_POST[name]',$_POST[parent],'$_POST[link]','$_POST[cond]')", $xoopsDB->prefix('tb_tabs'));

    #echo $quer;

    $xoopsDB->query($quer);

    redirect_header(XOOPS_URL . '/modules/tabmod/admin/index.php', 3, 'Thank you for your submission !');

    exit();
}

if ('do_edit_tab' == $op) {
    $quer = sprintf("update %s set  name='$_POST[name]', parent_id=$_POST[parent],link='$_POST[link]',activ_cond='$_POST[cond]' where id=$tid", $xoopsDB->prefix('tb_tabs'));

    $xoopsDB->query($quer);

    redirect_header(XOOPS_URL . '/modules/tabmod/admin/index.php', 3, 'Thank you for your submission !');

    exit();
}

if ('edit_tab' == $op) {
    show_etab_form($_GET[tid]);

    xoops_cp_footer();

    exit();
}
if ('mass' == $op) {
    #if (!is_array($_POST[ords])) {

    #	redirect_header(XOOPS_URL."/modules/tabmod/admin/index.php",3,"No items to order !");

    #	exit();

    #}

    $ret_message = '';

    if (is_array($_POST[del_items])) {
        foreach ($_POST[del_items] as $key => $value) {
            $quer = sprintf("select parent_id from %s where id=$value", $xoopsDB->prefix('tb_tabs'));

            $res = $xoopsDB->query($quer);

            $row = $xoopsDB->fetchArray($res);

            $quer = sprintf("update  %s set parent_id=$row[parent_id] where parent_id=$value", $xoopsDB->prefix('tb_tabs'));

            $xoopsDB->query($quer);

            $quer = sprintf("delete from %s where id=$value", $xoopsDB->prefix('tb_tabs'));

            $xoopsDB->query($quer);
        }

        $ret_message .= 'Items was deleted <br>';
    }

    if (is_array($_POST[ords])) {
        foreach ($_POST[ords] as $key => $value) {
            $quer = sprintf("update %s set  tb_ord='$value' where id=$key", $xoopsDB->prefix('tb_tabs'));

            $xoopsDB->query($quer);
        }

        $ret_message .= 'Items was updated <br>';
    }

    if ($ret_message) {
        redirect_header(XOOPS_URL . '/modules/tabmod/admin/index.php', 3, (string)$ret_message);

        exit();
    }  

    redirect_header(XOOPS_URL . '/modules/tabmod/admin/index.php', 3, 'No data');

    exit();
}

$xoopstree = new XoopsTree($xoopsDB->prefix('tb_tabs'), 'id', 'parent_id');
$arr = $xoopstree->getChildTreeArray(0, 'tb_ord DESC');

echo "<form method=post action='?op=mass'>";
OpenTable();

echo sprintf('<tr class=head><td>Tab Name</td><td ></td><td>Weight</td><td>Delete</td><td>Condition</td></tr>');
foreach ($arr as $ar) {
    echo sprintf(
        "<tr><td>$ar[prefix]<a href='%s/$ar[link]'>$ar[name]</a></td><td><a href='?op=edit_tab&tid=$ar[id]'>edit</a></td><td><input name='ords[$ar[id]]'type=text size=3 value='$ar[tb_ord]'></td><td><input type=checkbox name='del_items[]' value='$ar[id]'></td><td>$ar[activ_cond]</td></tr>",
        XOOPS_URL
    );
}
echo '<tr><td colspan=3 align=left><input type=submit ></td></tr>';

CloseTable();
echo '</form>';

show_tab_form();

exit();

function show_tab_form()
{
    global $xoopsDB;

    $form = new XoopsThemeForm('Add tab', 'tabs_form', 'index.php');

    $element = new XoopsFormText('Tab Name', 'name', 50, 255);

    $form->addElement($element);

    $select = new XoopsFormSelect('Parent', 'parent');

    $quer = sprintf('select id ,name from %s', $xoopsDB->prefix('tb_tabs'));

    $res = $xoopsDB->query($quer);

    $opts[0] = 'ROOT_PATH';

    #include ("../../../class/xoopstree.php");

    $xoopstree = new XoopsTree($xoopsDB->prefix('tb_tabs'), 'id', 'parent_id');

    $tree = $xoopstree->getChildTreeArray(0, 'tb_ord DESC');

    foreach ($tree as $opt) {
        $opts[$opt[id]] = $opt[prefix] . $opt[name];
    }

    $select->addOptionArray($opts);

    $form->addElement($select);

    $element1 = new XoopsFormText('Link', 'link', 50, 255);

    $form->addElement($element1);

    $element2 = new XoopsFormText('Condition', 'cond', 50, 255);

    $element2->setDescription('Hilight condition .Use $_GET[] , $_POST[] , $moduledir , $query <br> <b>example:</b> ');

    $form->addElement($element2);

    $submit_tray = new XoopsFormElementTray('');

    $submit_button = new XoopsFormButton('', 'submit', 'Submit', 'submit');

    $submit_tray->addElement($submit_button);

    $form->addElement($submit_tray);

    $op_hidden = new XoopsFormHidden('op', 'do_add_tab');

    $form->addElement($op_hidden);

    #	echo "<h4>"._AM_POLLCONF."</h4>";

    $form->display();
}

function show_etab_form($id)
{
    global $xoopsDB;

    $quer = sprintf("select * from %s where id=$id", $xoopsDB->prefix('tb_tabs'));

    $res_g = $xoopsDB->query($quer);

    $fields = $xoopsDB->fetchArray($res_g);

    $form = new XoopsThemeForm('Edit tab ', 'tabs_form', "index.php?tid=$id");

    $element = new XoopsFormText('Tab Name', 'name', 50, 255, $fields[name]);

    $form->addElement($element);

    $select = new XoopsFormSelect('Parent', 'parent', $fields[parent_id]);

    $quer = sprintf('select id ,name from %s', $xoopsDB->prefix('tb_tabs'));

    $res = $xoopsDB->query($quer);

    $opts[0] = 'ROOT_PATH';

    #include ("../../../class/xoopstree.php");

    $xoopstree = new XoopsTree($xoopsDB->prefix('tb_tabs'), 'id', 'parent_id');

    $tree = $xoopstree->getChildTreeArray(0, 'tb_ord DESC');

    foreach ($tree as $opt) {
        $opts[$opt[id]] = $opt[prefix] . $opt[name];
    }

    $select->addOptionArray($opts);

    $form->addElement($select);

    $element1 = new XoopsFormText('Link', 'link', 50, 255, $fields[link]);

    $form->addElement($element1);

    $element2 = new XoopsFormText('Condition', 'cond', 50, 255, $fields[activ_cond]);

    $element2->setDescription('Hilight condition .Use $_GET[] , $_POST[] , $moduledir , $query <br> <b>example:</b> ');

    $form->addElement($element2);

    $submit_tray = new XoopsFormElementTray('');

    $submit_button = new XoopsFormButton('', 'submit', 'Submit', 'submit');

    $submit_tray->addElement($submit_button);

    $form->addElement($submit_tray);

    $op_hidden = new XoopsFormHidden('op', 'do_edit_tab');

    $form->addElement($op_hidden);

    #	echo "<h4>"._AM_POLLCONF."</h4>";

    $form->display();
}



