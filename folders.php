<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

define('IN_CMBB', TRUE);
include('constants.php');
$root = 'assets/user_upload/';

if (isset($_GET['u'])) {
    $dir       = $root.$_GET['u'].'/';
    $structure = listfolders($dir);
    //var_dump($structure);
    echo json_encode($structure);
}
else {
    echo json_encode('');
}

function listfolders($dir)
{
    $dh     = scandir($dir);
    $return = array();


    foreach ($dh as $folder)
    {
        if ($folder != '.' && $folder != '..' && $folder != 'index.html' && $folder != 'Thumbs.db') {
            if (is_dir($dir.'/'.$folder)) {
                $subs = listfolders($dir.'/'.$folder);
                foreach ($subs as $sub)
                {
                    $return[] = $sub;
                }
            }
            else {
                $dir = str_replace('//', '/', $dir);
                // $path = str_replace($root, '', $dir);

                $return[] = array(
                    'image' => CMBB_ROOT.'/'.$dir.'/'.$folder,
                    'folder' => '',
                );
            }
        }
    }
    return $return;
}
