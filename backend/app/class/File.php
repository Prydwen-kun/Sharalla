<?php
class File
{
    private $id;
    private $title;
    private $description;
    private $size;
    private $path;
    private $upload_date;
    private $uploader_id;
    private $uploader;
    private $extension;
    private $type;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of size
     */ 
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of size
     *
     * @return  self
     */ 
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */ 
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of upload_date
     */ 
    public function getUpload_date()
    {
        return $this->upload_date;
    }

    /**
     * Set the value of upload_date
     *
     * @return  self
     */ 
    public function setUpload_date($upload_date)
    {
        $this->upload_date = $upload_date;

        return $this;
    }

    /**
     * Get the value of uploader_id
     */ 
    public function getUploader_id()
    {
        return $this->uploader_id;
    }

    /**
     * Set the value of uploader_id
     *
     * @return  self
     */ 
    public function setUploader_id($uploader_id)
    {
        $this->uploader_id = $uploader_id;

        return $this;
    }

    /**
     * Get the value of uploader
     */ 
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * Set the value of uploader
     *
     * @return  self
     */ 
    public function setUploader($uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * Get the value of extension
     */ 
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the value of extension
     *
     * @return  self
     */ 
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
