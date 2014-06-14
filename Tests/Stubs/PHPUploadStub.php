<?php
namespace Joomla\Filesystem;

function move_uploaded_file($filename, $destination)
{
    //Copy file
    return copy($filename, $destination);
}

function is_uploaded_file($filename)
{
    //Check only if file exists
    return file_exists($filename);
}
?>