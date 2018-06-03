<?php
namespace Joomla\Filesystem;

function move_uploaded_file($filename, $destination)
{
    return copy($filename, $destination);
}

function is_uploaded_file($filename)
{
    return file_exists($filename);
}
