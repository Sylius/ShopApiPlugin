<?php
/**
 * Created by PhpStorm.
 * User: iicux
 * Date: 16.11.19
 * Time: 19:04
 */

namespace Sylius\ShopApiPlugin\Transformer;

trait Transformer
{

    public $viewClass;

    public function setDefaultIncludes($includes)
    {
        if(is_array($includes)){
            $this->defaultIncludes = $includes;
        }
    }

    protected function generate($model)
    {
        $viewClass = new $this->viewClass();
        foreach ($this->defaultIncludes as $key=>$field) {
            if ( ! is_array($field)) {
                $name = 'get' . ucfirst($field);
            } else {
                $name = 'get' . ucfirst($key);
            }
            $viewClass = $this->{$name}($model, $viewClass);
        }

        return $viewClass;
    }

}
