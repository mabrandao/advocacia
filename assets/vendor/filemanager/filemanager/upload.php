<?php

if (!isset($config)){
  $config = include 'config/config.php';
}

require_once('UploadHandler.php');

if (session_id() == '') {
    session_start();
}

mb_internal_encoding('UTF-8');

if (isset($_POST['path'])) {
    $storeFolder = $_POST['path'];
} else {
    $storeFolder = $config['current_path'];
}

$options = array(
    'upload_dir' => $storeFolder,
    'upload_url' => $config['base_url'] . $config['upload_dir'],
    'accept_file_types' => '/\.(' . implode('|', array_merge(
        $config['ext_img'],
        $config['ext_file'],
        $config['ext_music'],
        $config['ext_video']
    )) . ')$/i',
    'max_file_size' => $config['MaxSizeUpload'] * 1024 * 1024,
    'print_response' => true
);

$upload_handler = new UploadHandler($options);

header('Content-Type: application/json');

$info = $upload_handler->post();
if (isset($info['files'][0])) {
    $file = $info['files'][0];
    if (isset($file->error)) {
        echo json_encode(array('error' => $file->error));
    } else {
        echo json_encode(array(
            'location' => $config['base_url'] . $config['upload_dir'] . $file->name
        ));
    }
} else {
    echo json_encode(array('error' => 'Erro no upload do arquivo'));
}
