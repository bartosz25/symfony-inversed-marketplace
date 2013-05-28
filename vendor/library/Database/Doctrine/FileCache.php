<?php
namespace Database\Doctrine;

use Doctrine\Common\Cache\Cache;

class FileCache implements Cache
{

  /** 
   * Constant with extension of cache files.
   */
  const EXTENSION = '.cache';

  /**
   * Lifetime of cache file. If 0 the cache is stocked without time limit.
   * @access public
   * @var int
   */
  public $maxLifeTime = 0;

  /**
   * Cache base dir.
   * @acces private
   * @var string
   */
  private $cacheBaseDir;

  /**
   * Structure of cache directories.
   * @access private
   * @var array
   */
  private $cacheStructure;

  /**
   * Last cleaning directory.
   * @access private
   * @var string
   */
  private $lastCleanedDir;
  
  /**
   * Last directory to clean (before start of cleaning process)
   * @access private
   * @var string
   */
  private $lastDirectoryToClean;

  /** 
   * Cache base directory.
   */
  public function setBaseDir($dir)
  {
    $this->cacheBaseDir = $dir;
  }
  public function getBaseDir()
  {
    return $this->cacheBaseDir;
  }

  /**
   * Cache structure.
   */
  public function setCacheStructure($structure)
  {
    $this->cacheStructure = $structure;
  }
  public function getCacheStructure()
  {
    return $this->cacheStructure;
  }

  /**
   * Last cleaned directory.
   */
  public function setLastCleanedDirectory($directory)
  {
    $this->lastCleanedDir = $directory;
  }
  public function getLastCleanedDirectory()
  {
    return $this->lastCleanedDir;
  }

  /**
   * Last directory to clean.
   */
  public function setLastDirectoryToClean($directory)
  {
    $this->lastDirectoryToClean = $directory;
  }
  public function getLastDirectoryToClean()
  {
    return $this->lastDirectoryToClean;
  }
  public function localizeLastDirectoryToClean($dir)
  {
    $lastDir = $dir;
    $files = scandir($dir);
    for($i = 2; $i < count($files); $i++)
    {
      if(!is_file(($nextDir = $dir."/".$files[$i])))
      {
        $lastDir = $this->localizeLastDirectoryToClean($nextDir);
      }
    }
    return $this->lastDirectoryToClean = str_replace("//", "/", $lastDir);
  }

// abstract method, must be implemented
  public function contains($d) {}

  /**
   * Gets cache by his id.
   * @access public
   * @param string $id Cache's id.
   * @return mixed Null if cache not found, array when cached data found
   */
  public function fetch($id)
  {
    if(!file_exists($this->cacheBaseDir.$id.self::EXTENSION))
    {
      return null;
    }
    else
    {
      $content = file_get_contents($this->cacheBaseDir.$id.self::EXTENSION);
      $data = (array)unserialize($content);
      if($data['stats']['lifetime'] == 0 || $data['stats']['lifetime'] <  time())
      {
        return $data['data'];
      }
      unlink($this->cacheBaseDir.$id.self::EXTENSION);
      return null;
    }
  }

  /** 
   * Saves cache data into a file. It's based on write() function from 
   * Symfony 1.4 framework. 
   * @param string $name Cache name.
   * @param array $data Data to save.
   * @param int $lifeTime Cache file lifetime. 
   * @param array $tags Cache's tags. 
   * @return void
   */
  public function save($name, $data, $lifeTime = 0, $tags = array())
  {
    $dirs = explode('/', $name);
    unset($dirs[count($dirs)-1]);
    $dir = implode('/', $dirs);
    if(!is_dir($this->cacheBaseDir.$dir))
    {
      mkdir($this->cacheBaseDir.$dir, 0777, true);
    }
    // set cache lifetime
    $result = array('stats' => array('tags' => $tags, 'lifetime' => $lifeTime),
    'data' => $data);
    file_put_contents($this->cacheBaseDir.$name.self::EXTENSION, serialize($result));
  }

  /**
   * Delete cache.
   * @access public
   * @param string $id Cache's name.
   * @return bool True if deleted correctly, false otherwise
   */
  public function delete($id)
  {
    return (bool)(@unlink($id));
  }

  /** 
   * Function to clean all cache files in one directory. 
   * @param array $paths Array with caches directories to clean.
   * @return bool True if all files deleted correctly, false otherwise.
   */
  public function cleanDirCache($dir = '') 
  {
// echo "Cleaning {$dir} <br />";
    $this->lastCleanedDir = str_replace("//", "/", $dir);
// TODO : gérer le cas où dans le répertoire se trouvent les sous-répertoires
    $errors = array();
    if(file_exists($dir))
    {
      $files = scandir($dir);
      for($i = 2; $i < count($files); $i++)
      {
        if(!is_file($dir."/".$files[$i]))
        {
          $this->cleanDirCache($dir."/".$files[$i]);
        }
        elseif(!$this->delete($dir."/".$files[$i]))
        {
// echo "Deleting {$dir}/{$files[$i]} <br />";
          $errors[] = $files[$i];
        }
      }
    }
    return (bool)(count($errors) == 0);
  }


  /**
   * Get real lifetime of cache.
   * @access private
   * @return array
   */
  private function getExpiredTime()
  {
    return time() + $this->maxLifeTime;
  }

  /**
   * Gets cache files extension.
   * @access public
   * @return string Cache extension name.
   */
  public function getExtension()
  {
    return self::EXTENSION;
  }

}