<?php

function uploadFile()
{
    $target_dir = "downloads/questionimg/";
    $target_file = $target_dir .uniqid().'-'. basename($_FILES["questionImg"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["questionImg"]["tmp_name"]);
    if($check == false) {
        $uploadOk = 0;
    }

    // Check file size 500KB
    if ($_FILES["questionImg"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $uploadOk = 0;
    }

    // upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["questionImg"]["tmp_name"], $target_file)) {
            return $target_file;
        }
    }

    return 0;

}
?>