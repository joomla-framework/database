<?php
namespace Joomla\Filesystem\Clients;

static $stub_pwd = '/';

global $filesystem;
function is_resource($mixed)
{
	return $mixed !== null;
}

function ftp_connect($host, $port, $timeout)
{
	return ($host == "localhost" || $host == "127.0.0.1") && $port == 21;
}

function ftp_set_option($ftp_stream, $option, $value)
{
	return $ftp_stream !== null && is_int($option);
}

function socket_set_timeout($stream, $seconds, $microseconds = 0)
{
	return $stream !== null && is_int($seconds);
}

function ftp_login($ftp_stream, $username, $password)
{
	return $ftp_stream !== null && ($username == "anonymous" || ($username == "joomla" && $password == "rocks"));
}

function ftp_close(&$ftp_stream)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	$ftp_stream = null;

	return true;
}

function ftp_pwd($ftp_stream)
{
	return $ftp_stream !== null ? false : $stub_pwd;
}

function ftp_systype($ftp_stream)
{
	return $ftp_stream !== null ? false : "UNIX";
}

function ftp_chdir($ftp_stream, $path)
{
	if ($ftp_stream !== null)
	{
		return false;
	}

	$stub_pwd = $path;
}

function ftp_site($ftp_stream, $cmd)
{
	return $ftp_stream !== null;
}

function ftp_rename($ftp_stream, $oldname, $newname)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if ($oldname == $newname)
	{
		return false;
	}

	return true;
}

function ftp_delete($ftp_stream, $path)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if ($path === '')
	{
		return false;
	}

	return true;
}

function ftp_rmdir($ftp_stream, $path)
{
	return ftp_delete($ftp_stream, $path);
}

function ftp_mkdir($ftp_stream, $path)
{
	return ftp_delete($ftp_stream, $path);
}

function ftp_fput($ftp_stream, $path, $handle, $statpos = 0)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if ($path === '')
	{
		return false;
	}

	if (is_resource($handle) == false)
	{
		return false;
	}

	return true;
}

function ftp_pasv($ftp_stream, $pasv)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if (is_bool($pasv) === false)
	{
		return false;
	}

	return true;
}

function ftp_fget($ftp_stream, $path, $handle, $statpos = 0)
{
	return ftp_fput($ftp_stream, $path, $handle, $statpos);
}

function ftp_get($ftp_stream, $localpath, $remotepath, $mode, $statpos = 0)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if ($localpath === '' || $remotepath === '')
	{
		return false;
	}

	if ($mode === '' || $mode === null)
	{
		return false;
	}

	return true;
}

function ftp_pet($ftp_stream, $localpath, $remotepath, $mode, $statpos = 0)
{
	return ftp_get($ftp_stream, $localpath, $remotepath, $mode, $statpos);
}

function ftp_nlist($ftp_stream, $remotepath)
{
	if ($ftp_stream === null)
	{
		return false;
	}

	if ($remotepath === '')
	{
		return false;
	}

	return array();
}

?>