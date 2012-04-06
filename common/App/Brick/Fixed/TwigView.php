<?php
class App_Brick_Fixed_TwigView implements Zend_View_Interface
{

    /**
     * assigned vars
     * @var array
     */
    protected $_assigned = array();

    /**
     * twig environment
     * @var Twig_Environment
     */
    protected $_twig;
    
    protected $_extName = null;
    /**
     * class constructor
     *
     * @param string $templatePath
     * @param array $envOptions options to set on the environment
     * @return void
     */
    public function __construct()
    {
        $this->_twig = new Twig_Environment(null);
    }

    /**
     * Set the template loader
     *
     * @param Twig_LoaderInterface $loader
     * @return void
     */
    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->_twig->setLoader($loader);
    }
    
    /**
     * Get the template loader
     *
     * @return Twig_LoaderInterface
     */
    public function getLoader()
    {
        return $this->_twig->getLoader();
    }
    
    /**
     * Get the twig environment
     * 
     * @return Twig_Environment
     */
    public function getEngine()
    {
        return $this->_twig;
    }

    /**
     * Set the path to the templates
     *
     * @param string $path The directory to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {
        $loader = new Twig_Loader_Filesystem($path);
        $this->setLoader($loader);
        return $this;
    }

    
    public function addScriptPath($path)
    {
    	$loader = $this->getLoader();
    	$loader->addPath($path);
    	return $this;
    }
    
    /**
     * Retrieve the current template directory
     *
     * @return string
     */
    public function getScriptPaths()
    {
        $loader = $this->getLoader();
        $path = ($loader instanceof Twig_Loader_FileSystem) 
            ? $loader->getPaths()
            : '';

        return $path;
    }

    /**
     * No basepath support on twig, therefore alias for "setScriptPath()"
     *
     * @see setScriptPath()
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function setBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }

    /**
     * No basepath support on twig, therefore alias for "setScriptPath()"
     *
     * @see setScriptPath()
     * @param string $path
     * @param string $prefix Unused
     * @return void
     */
    public function addBasePath($path, $prefix = 'Zend_View')
    {
        return $this->setScriptPath($path);
    }
    
    /**
     * Assign a variable to the template
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->assign($key, $val);
    }

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_assigned[$key]);
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->_assigned[$key]);
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing
     * an array of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or
     * array of key => value pairs)
     * @param mixed $value (Optional) If assigning a named variable,
     * use this as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_assigned = array_merge($this->_assigned, $spec);
        } else if(is_object($spec)) {
        	$spec = get_object_vars($spec);
        	$this->_assigned = array_merge($this->_assigned, $spec);
        } else {
        	$this->_assigned[$spec] = $value;
        }
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via
     * {@link assign()} or property overloading
     * ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_assigned = array();
    }
    
    /**
     * Processes a template and returns the output.
     *
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name)
    {
        $template = $this->_twig->loadTemplate($name);
        return $template->render($this->_assigned);
    }
}

