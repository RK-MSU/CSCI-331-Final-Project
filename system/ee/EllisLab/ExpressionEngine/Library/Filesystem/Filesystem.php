<?php

namespace EllisLab\ExpressionEngine\Library\Filesystem;

use FilesystemIterator;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2016, EllisLab, Inc.
 * @license		https://expressionengine.com/license
 * @link		https://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Filesystem Library
 *
 * @package		ExpressionEngine
 * @subpackage	Filesystem
 * @category	Library
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
class Filesystem {

	/**
	 * Read a file from disk
	 *
	 * @param String $path File to read
	 * @return String File contents
	 */
	public function read($path)
	{
		if ( ! $this->exists($path))
		{
			throw new FilesystemException("File not found: {$path}");
		}
		elseif ( ! $this->isFile($path))
		{
			throw new FilesystemException("Not a file: {$path}");
		}
		elseif ( ! $this->isReadable($path))
		{
			throw new FilesystemException("Cannot read file: {$path}");
		}

		return file_get_contents($this->normalize($path));
	}

	/**
	 * Read a file from disk line-by-line, good for large text files
	 *
	 * @param String $path File to read
	 * @return Callable Callback to call for each line of the file
	 */
	public function readLineByLine($path, Callable $callback)
	{
		if ( ! $this->exists($path))
		{
			throw new FilesystemException("File not found: {$path}");
		}
		elseif ( ! $this->isFile($path))
		{
			throw new FilesystemException("Not a file: {$path}");
		}
		elseif ( ! $this->isReadable($path))
		{
			throw new FilesystemException("Cannot read file: {$path}");
		}

		$pointer = fopen($path, 'r');

		while ( ! feof($pointer))
		{
			$callback(fgets($pointer));
		}

		fclose($pointer);
	}

	/**
	 * Write a file to disk
	 *
	 * @param String $path File to write to
	 * @param String $data Data to write
	 * @param bool $overwrite Overwrite existing files?
	 * @param bool $append Append to existing file?
	 */
	public function write($path, $data, $overwrite = FALSE, $append = FALSE)
	{
		$path = $this->normalize($path);

		if ($this->isDir($path))
		{
			throw new FilesystemException("Cannot write file, path is a directory: {$path}");
		}
		elseif ($this->isFile($path) && $overwrite == FALSE && $append == FALSE)
		{
			throw new FilesystemException("File already exists: {$path}");
		}

		$flags = LOCK_EX;
		if ($overwrite == FALSE && $append == TRUE)
		{
			$flags = FILE_APPEND | LOCK_EX;
		}

		file_put_contents($path, $data, $flags);

		$this->ensureCorrectAccessMode($path);
	}

	/**
	 * Make a new directory
	 *
	 * @param String $path Directory to create
	 * @param bool $with_index Add EE's default index.html file in the new dir?
	 * @return bool Success or failure of mkdir()
	 */
	public function mkDir($path, $with_index = TRUE)
	{
		$path = $this->normalize($path);
		$result = @mkdir($path, DIR_WRITE_MODE, TRUE);

		if ( ! $result)
		{
			return FALSE;
		}

		if ($with_index)
		{
			$this->addIndexHtml($path);
		}

		$this->ensureCorrectAccessMode($path);

		return TRUE;
	}

	/**
	 * Delete a file or directory
	 *
	 * @param String $path File or directory to delete
	 */
	public function delete($path)
	{
		if ($this->isDir($path))
		{
			return $this->deleteDir($path);
		}

		return $this->deleteFile($path);
	}

	/**
	 * Delete a file
	 *
	 * @param String $path File to delete
	 */
	public function deleteFile($path)
	{
		if ( ! $this->isFile($path))
		{
			throw new FilesystemException("File does not exist {$path}");
		}

		return @unlink($this->normalize($path));
	}

	/**
	 * Delete a directory
	 *
	 * @param String $path Directory to delete
	 * @param bool $leave_empty Keep the empty root directory?
	 */
	public function deleteDir($path, $leave_empty = FALSE)
	{
		$path = rtrim($path, '/');

		if ( ! $this->isDir($path))
		{
			throw new FilesystemException("Directory does not exist {$path}.");
		}

		if ($this->attemptFastDelete($path))
		{
			return TRUE;
		}

		$contents = new FilesystemIterator($this->normalize($path));

		foreach ($contents as $item)
		{
			if ($item->isDir())
			{
				$this->deleteDir($item->getPathname());
			}
			else
			{
				$this->deleteFile($item->getPathName());
			}
		}

		if ( ! $leave_empty)
		{
			@rmdir($this->normalize($path));
		}

		return TRUE;
	}

	/**
	 * Gets the contents of a directory as a flat array, with the option of
	 * returning a recursive listing
	 *
	 * @param String $path Directory to search
	 * @param bool $recursive Whether or not to do a recursive search
	 * @param array Array of all paths found inside the specified directory
	 */
	public function getDirectoryContents($path, $recursive = FALSE)
	{
		if ( ! $this->exists($path) OR ! $this->isDir($path))
		{
			throw new FilesystemException('Cannot get contents of path, either invalid or not a directory: '.$path);
		}

		$contents = new FilesystemIterator($this->normalize($path));
		$contents_array = [];

		foreach ($contents as $item)
		{
			if ($item->isDir() && $recursive)
			{
				$contents_array += $this->getDirectoryContents($item->getPathname(), $recursive);
			}
			else
			{
				$contents_array[] = $item->getPathName();
			}
		}

		return $contents_array;
	}

	/**
	 * Empty a directory
	 *
	 * @param String $path Directory to empty
	 * @param bool $add_index Add EE's default index.html file to the directory
	 */
	public function emptyDir($path, $add_index = TRUE)
	{
		$this->deleteDir($path, TRUE);
		$this->addIndexHtml($path);
	}

	/**
	 * Attempt to delete a file using the OS method
	 *
	 * We can't always do this, but it's much, much faster than iterating
	 * over directories with many children.
	 *
	 * @param String $path
	 */
	protected function attemptFastDelete($path)
	{
		$normal_path = $this->normalize($path);
		$path_delete = $normal_path.'_delete_'.mt_rand();

		@exec("mv {$normal_path} {$path_delete}", $out, $ret);

		if (isset($ret) && $ret == 0)
		{
			@exec("rm -r -f {$path_delete}");
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Rename a file or directory
	 *
	 * @param String $source File or directory to rename
	 * @param String $dest New location for the file or directory
	 */
	public function rename($source, $dest)
	{
		if ( ! $this->exists($source))
		{
			throw new FilesystemException("Cannot rename non-existent path: {$source}");
		}
		elseif ($this->exists($dest))
		{
			throw new FilesystemException("Cannot rename, destination already exists: {$dest}");
		}

		rename(
			$this->normalize($source),
			$this->normalize($dest)
		);

		$this->ensureCorrectAccessMode($dest);
	}

	/**
	 * Copy a file or directory
	 *
	 * @param String $source File or directory to copy
	 * @param Stirng $dest Path to the duplicate
	 */
	public function copy($source, $dest)
	{
		if ( ! $this->exists($source))
		{
			throw new FilesystemException("Cannot copy non-existent path: {$source}");
		}

		if ($this->isDir($source))
		{
			$this->recursiveCopy($source, $dest);
		}
		else
		{
			copy(
				$this->normalize($source),
				$this->normalize($dest)
			);
		}

		$this->ensureCorrectAccessMode($dest);
	}

	/**
	 * Copies a directory to another directory by recursively iterating over its files
	 *
	 * @param String $source Directory to copy
	 * @param Stirng $dest Path to the duplicate
	 */
	protected function recursiveCopy($source, $dest)
	{
		$dir = opendir($source);
		@mkdir($dest);

		while(false !== ($file = readdir($dir)))
		{
			if (($file != '.') && ($file != '..'))
			{
				if ( is_dir($source . '/' . $file) )
				{
					$this->recursiveCopy($source . '/' . $file, $dest . '/' . $file);
				}
				else
				{
					copy($source . '/' . $file, $dest . '/' . $file);
				}
			}
		}

		closedir($dir);
	}

	/**
	 * Get the path to the parent directory
	 *
	 * @param String $path Path to extract dirname from
	 * @return String Path to the parent directory
	 */
	public function dirname($path)
	{
		return dirname($this->normalize($path));
	}

	/**
	 * Get the filename and extension
	 *
	 * @param String $path Path to extract basename from
	 * @return String Filename with extension
	 */
	public function basename($path)
	{
		return basename($this->normalize($path));
	}

	/**
	 * Get the filename without extension
	 *
	 * @param String $path Path to extract filename from
	 * @return String Filename without extension
	 */
	public function filename($path)
	{
		return pathinfo($this->normalize($path), PATHINFO_FILENAME);
	}

	/**
	 * Get the extension
	 *
	 * @param String $path Path to extract extension from
	 * @return String Extension
	 */
	public function extension($path)
	{
		return pathinfo($this->normalize($path), PATHINFO_EXTENSION);
	}

	/**
	 * Check if a path exists
	 *
	 * @param String $path Path to check
	 * @return bool Path exists?
	 */
	public function exists($path)
	{
		if ($path = $this->normalize($path))
		{
			return file_exists($path);
		}

		return FALSE;
	}

	/**
	 * Get the last modified time
	 *
	 * @param String $path Path to directory or file
	 * @return int Last modified time
	 */
	public function mtime($path)
	{
		if ( ! $this->exists($path))
		{
			throw new FilesystemException("File does not exist: {$path}");
		}

		return filemtime($this->normalize($path));
	}

	/**
	 * Touch a file or directory
	 *
	 * @param String $path File/directory to touch
	 * @param int Set the last modified time [optional]
	 */
	public function touch($path, $time = NULL)
	{
		if ( ! $this->exists($path))
		{
			throw new FilesystemException("Touching non-existent files is not supported: {$path}");
		}

		if (isset($time))
		{
			touch($this->normalize($path), $time);
		}
		else
		{
			touch($this->normalize($path));
		}
	}

	/**
	 * Check if a given path is a directory
	 *
	 * @param String $path Path to check
	 * @return bool Is a directory?
	 */
	public function isDir($path)
	{
		return is_dir($this->normalize($path));
	}

	/**
	 * Check if a given path is a file
	 *
	 * @param String $path Path to check
	 * @return bool Is a file?
	 */
	public function isFile($path)
	{
		return is_file($this->normalize($path));
	}

	/**
	 * Check if a path is readable
	 *
	 * @param String $path Path to check
	 * @return bool Is readable?
	 */
	public function isReadable($path)
	{
		return is_readable($this->normalize($path));
	}

	/**
	 * Change the access mode of a file
	 *
	 * @param String $path Path to Change
	 * @param Int Mode, please provide an octal
	 */
	public function chmod($path, $mode)
	{
		return @chmod($this->normalize($path), $mode);
	}

	/**
	 * Check if a file or directory is writable
	 *
	 * Does some extra checks for safe_mode windows servers. Yuck.
	 *
	 * @param String $path Path to check
	 * @return bool Is writable?
	 */
	public function isWritable($path)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($this->normalize($path));
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if ($this->isDir($path))
		{
			$path = rtrim($this->normalize($path), '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($path, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($path, DIR_WRITE_MODE);
			@unlink($path);
			return TRUE;
		}
		elseif (($fp = @fopen($path, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

	/**
	 * Returns the SHA1 hash for a file
	 *
	 * @param String $path Path to check
	 * @return String SHA1 hash of file
	 */
	public function sha1File($filename)
	{
		if ( ! $this->exists($filename))
		{
			throw new FilesystemException("File does not exist: {$filename}");
		}

		return sha1_file($filename);
	}

	/**
	 * Returns the amount of free bytes at a given path
	 *
	 * @param	String	$path	Path to check
	 * @return	Mixed	Number of bytes as a float, or FALSE on failure
	 */
	public function getFreeDiskSpace($path = '/')
	{
		return @disk_free_space($path);
	}

	/**
	 * include() a file
	 *
	 * @param	string	$filename	Full path to file to include
	 */
	public function include($filename)
	{
		include_once($filename);
	}

	/**
	 * Add EE's default index file to a directory
	 */
	protected function addIndexHtml($dir)
	{
		$dir = rtrim($dir, '/');

		if ( ! $this->isDir($dir))
		{
			throw new FilesystemException("Cannot add index file to non-existant directory: {$dir}");
		}

		if ( ! $this->isFile($dir.'/index.html'))
		{
			$this->write($dir.'/index.html', 'Directory access is forbidden.');
		}
	}

	/**
	 * Writing files and directories should respect the write modes
	 * specified. Otherwise on some crudy hosts you end up unable
	 * to change those files via FTP.
	 *
	 * @param String $path Path to ensure access to
	 */
	protected function ensureCorrectAccessMode($path)
	{
		if ($this->isDir($path))
		{
			$this->chmod($path, DIR_WRITE_MODE);
		}
		else
		{
			$this->chmod($path, FILE_WRITE_MODE);
		}
	}

	/**
	 * Normalize the path for a native function call
	 *
	 * This is used by classes that extend this one to, for example, root
	 * the filesystem in a specific location. It can also be used for sanity
	 * checks, but beware that it is slow.
	 */
	protected function normalize($path)
	{
		return $path;
	}
}

// EOF
