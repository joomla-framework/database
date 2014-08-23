<?php
/**
 * Renderer Package
 *
 * @copyright  Copyright (C) 2014 Roman Kinyakin. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace BabDev\Renderer;

/**
 * Twig class for rendering output.
 *
 * @since  1.0
 */
class TwigLoader extends \Twig_Loader_Filesystem 
{
    /**
     * Extension of template files
     */  
    protected $extension = '';
    
    public function setExtension($extension) 
    {
        $extension = ltrim($extension, '.'); //Remove dots in the beginning
        if (!empty($extension)) //If the extension is not empty add dot again
        {
            $extension = '.' . $extension;  
        }
        $this->extension = $extension;
    }
    
    protected function findTemplate($name)
    {
        $parts = explode('.', $name);
        
        $extension = count($parts > 1) ? '.' . end($parts) : '';
        
        if ($extension != $this->extension)
        {
            $name .= $this->extension;
        }
        return parent::findTemplate($name);
    }
    
}
