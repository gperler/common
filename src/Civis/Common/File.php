<?php

declare(strict_types=1);

namespace Civis\Common;

class File
{

    const EXCEPTION_FILE_DOES_NOT_EXIST = "File '%s' does not exist";

    const EXCEPTION_INVALID_XML = "File '%s' does not contain valid XML %s";

    const EXCEPTION_INVALID_JSON = "File '%s' does not contain valid JSON";

    /**
     * absolute path to file
     *
     * @var string
     */
    protected $absoluteFileName;


    /**
     * @param string $absoluteFileName
     */
    public function __construct($absoluteFileName)
    {
        $this->absoluteFileName = rtrim($absoluteFileName, '/');
    }


    /**
     * @return string
     */
    public function getAbsoluteFileName()
    {
        return $this->absoluteFileName;
    }


    /**
     * @return string
     */
    public function getFileName()
    {
        return basename($this->absoluteFileName);
    }


    /**
     * @return string
     */
    public function getExtension()
    {
        return StringUtil::getEndAfterLast($this->getFileName(), ".");
    }


    /**
     * @param string $extension
     *
     * @return bool
     */
    public function hasExtension(string $extension)
    {
        return StringUtil::endsWith($this->absoluteFileName, $extension);
    }


    /**
     * @return string
     */
    public function getDirName()
    {
        return dirname($this->absoluteFileName);
    }


    /**
     * tells if the file is a file
     *
     * @return bool
     */
    public function isFile()
    {
        return is_file($this->absoluteFileName);
    }


    /**
     * tells if the file is a directory
     *
     * @return bool
     */
    public function isDir()
    {
        return is_dir($this->absoluteFileName);
    }


    /**
     * tries to delete a file
     *
     * @return bool
     */
    public function delete()
    {
        if (!$this->exists()) {
            return false;
        }
        if ($this->isDir()) {
            return rmdir($this->absoluteFileName);
        }

        return unlink($this->absoluteFileName);
    }


    /**
     *
     */
    public function deleteRecursively()
    {
        if ($this->isDir()) {
            $fileList = $this->scanDir();

            foreach ($fileList as $file) {
                if ($file->isDir()) {
                    $file->deleteRecursively();
                } else {
                    $file->delete();
                }
            }
            rmdir($this->absoluteFileName);
        }
    }


    /**
     * tells if the file exists
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->absoluteFileName);
    }


    /**
     * creates needed directories recursively
     *
     * @param int $mode
     *
     * @return bool
     */
    public function createDir($mode = 0777)
    {
        return is_dir($this->absoluteFileName) || mkdir($this->absoluteFileName, $mode, true);
    }


    /**
     * scans a directory and returns a list of Files
     *
     * @return File[]
     */
    public function scanDir()
    {
        // only possible in directories
        if (!$this->isDir() || !$this->exists()) {
            return null;
        }

        $fileList = [];
        $fileNameList = scandir($this->absoluteFileName);
        foreach ($fileNameList as $fileName) {
            // do not add . and ..
            if ($fileName === "." or $fileName === "..") {
                continue;
            }
            $absoluteFileName = $this->absoluteFileName . "/" . $fileName;
            $fileList [] = new File ($absoluteFileName);
        }

        return $fileList;
    }


    /**
     * Finds the first occurence of the given filename
     *
     * @param string $fileName
     *
     * @return null|File
     */
    public function findFirstOccurenceOfFile(string $fileName)
    {
        if (!$this->isDir() || !$this->exists()) {
            return null;
        }

        foreach ($this->scanDir() as $file) {
            if ($file->getFileName() === $fileName) {
                return $file;
            }

            if ($file->isDir()) {
                $result = $file->findFirstOccurenceOfFile($fileName);
                if ($result !== null) {
                    return $result;
                }
            }
        }
        return null;
    }


    /**
     * @param string $extension
     *
     * @return File[]
     */
    public function findFileList(string $extension): array
    {
        if (!$this->isDir() || !$this->exists()) {
            return [];
        }

        $result = [];
        foreach ($this->scanDir() as $file) {
            if ($file->isDir()) {
                $result = array_merge($result, $file->findFileList($extension));
                continue;
            }
            if ($file->hasExtension($extension)) {
                $result[] = $file;
            }
        }
        return $result;
    }


    /**
     * @return \XSLTProcessor
     * @throws FileException
     */
    public function loadAsXSLTProcessor()
    {
        $this->checkFileExists();
        $xsl = new \XSLTProcessor();
        $xsl->importStylesheet($this->loadAsXML());
        return $xsl;
    }


    /**
     * @return \DomDocument
     * @throws FileException
     */
    public function loadAsXML()
    {
        $this->checkFileExists();
        $xml = new \DomDocument ();
        libxml_use_internal_errors(true);
        $result = $xml->load($this->absoluteFileName);
        if ($result) {
            return $xml;
        }
        $messageObject = ArrayUtil::getFromArray(libxml_get_errors(), 0);
        $libXMLMessage = ObjectUtil::getFromObject($messageObject, "message");
        $message = sprintf(self::EXCEPTION_INVALID_XML, $this->absoluteFileName, $libXMLMessage);
        $e = new FileException(libxml_get_errors()[0]->message);
        libxml_clear_errors();
        throw $e;
    }


    /**
     * @return array
     * @throws FileException
     */
    public function loadAsJSONArray(): array
    {
        $this->checkFileExists();
        $array = json_decode($this->getContents(), true);
        if ($array === null || !is_array($array)) {
            $message = sprintf(self::EXCEPTION_INVALID_JSON, $this->absoluteFileName);
            throw new FileException($message);
        }
        return $array;
    }


    /**
     * @return mixed
     * @throws FileException
     */
    public function loadAsJSONObject()
    {
        $this->checkFileExists();
        return json_decode($this->getContents());
    }


    /**
     * @return false|string
     * @throws FileException
     */
    public function getContents()
    {
        $this->checkFileExists();
        return file_get_contents($this->absoluteFileName);
    }


    /**
     * @param string $data
     * @param bool $createDir
     * @param int $mode
     */
    public function putContents(string $data, bool $createDir = true, int $mode = 0777)
    {
        if ($createDir) {
            $dir = new File($this->getDirName());
            $dir->createDir($mode);
        }
        file_put_contents($this->absoluteFileName, $data);
    }


    /**
     * @throws FileException
     */
    protected function checkFileExists()
    {
        if ($this->exists()) {
            return;
        }
        $message = sprintf(self::EXCEPTION_FILE_DOES_NOT_EXIST, $this->absoluteFileName);
        throw new FileException($message);
    }


    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->absoluteFileName;
    }

}
