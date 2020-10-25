<?php

require dirname(__DIR__, 2) . '/mainfile.php';
$dirname = 'news';
$table = $xoopsDB->prefix('tb_tabs');
$quer = sprintf("select * from $table ", $xoopsDB->prefix('tb_tabs'));

$res = $xoopsDB->query($quer);

echo "<div id='content-nav'>";

while (false !== ($row = $xoopsDB->fetchArray($res))) {
    eval("\$condition=$row[activ_cond];");

    if ($condition) {
        $select_row = $row;
    }
}
echo get_child_menu($select_row[id]);

echo "</div>\n";

function get_child_menu($sel_id, $child_out = '')
{
    global $xoopsDB;

    $table = $xoopsDB->prefix('tb_tabs');

    $quer = "select parent_id from $table where id=$sel_id";

    $res = $xoopsDB->query($quer);

    $row = $xoopsDB->fetchArray($res);

    $pid = $row[parent_id];

    $quer = "select * from $table where parent_id=$pid";

    $res = $xoopsDB->query($quer);

    if (0 == $xoopsDB->getRowsNum($res)) {
        return $child_out;
    }

    while (false !== ($row = $xoopsDB->fetchArray($res))) {
        $selected = '';

        $child = '';

        if ($row[id] == $sel_id) {
            $selected = "class='here'";

            $next[id] = $row[parent_id];

            $child = $child_out;
        }

        $output .= sprintf("<li><a href='$xoops_url/$row[link]' $selected>$row[name]</a>$child</li>");
    }

    $output = get_child_menu(
        $next[id],
        sprintf(
            "<ul %s >\n %s</ul>\n",
            0 == $pid ? "id='navigation'" : '',
            $output
        )
    );

    return $output;
}

?>
