<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;
use SimpleXMLElement;

trait ToXml {

    /**
     * Build an XML document
     *
     * @param string $method Method used to submit the form
     * @param SimpleXMLElement|null $parent Parent node to build complex nested structures
     * @param boolean $wrap Wrap root element with plural endpoint tag
     * @return string
     */
    protected function toXml($method = 'post', SimpleXMLElement &$parent = null, $wrap = false)
    {

        //Generating root element

        $singularEndpoint = str_singular($this->endpoint);

        //var_dump("<${singularEndpoint}/>");

        if (!is_null($parent))
        {

            $rootXml = $parent;

        }
        else
        {

            if ($wrap)
            {
                $_rootXml = (new SimpleXMLElement("<" . $this->endpoint ."/>"));
                $rootXml = $_rootXml->addChild($singularEndpoint);
            }
            else
            {
                $rootXml = (new SimpleXMLElement("<" . $singularEndpoint ."/>"));
            }

        }

        foreach ($this->attributes as $name => $attribute)
        {

            if(!$this->attributeNeedsSubmitting($name))
            {
                continue;
            }

            if (is_subclass_of($attribute, XeroModel::class))
            {

                $childXml = $rootXml->addChild(str_singular($name));

                $attribute->toXml($method, $childXml);

            }
            elseif($attribute instanceof Carbon)
            {

                $rootXml->addChild($name, $attribute->toDateTimeString());

            }
            elseif($attribute instanceof Collection)
            {

                $collection = $rootXml->addChild($name);

                $attribute->map(function($item) use ($collection, $name, $method) {


                    $childName = is_null($item->getOverriddenName()) ? str_singular($name) : $item->getOverriddenName();

                    $childXml = $collection->addChild($childName);

                    $item->toXml($method, $childXml);
                });
            }
            elseif(is_array($attribute))
            {
                $collection = $rootXml->addChild($name);

                foreach ($attribute as $sub)
                {
                    if (is_array($sub))
                    {
                        //var_dump($sub);
                        continue;
                    }

                    $collection->addChild(str_singular($name), $sub);
                }

            }
            else
            {
                $rootXml->addChild($name, $attribute);
            }
        }

        return $wrap ? $_rootXml->asXML() : $rootXml->asXML();
    }

    protected function attributeNeedsSubmitting($name, $excludeGuid = true)
    {
        if (!(isset($this->attrs[$name]) && is_array($this->attrs[$name])))
        {
            if ($excludeGuid)
            {
                return isset($this->attrs[$name]) ? $this->attrs[$name] == 'guid' : false;
            }

            return false;

        }

        if (in_array('put', $this->attrs[$name]) || in_array('post', $this->attrs[$name]) || ($excludeGuid && $this->attrs[$name]['type'] == 'guid'))
        {
            return true;
        }

        return false;
    }

    function append_simplexml(&$simplexml_to, &$simplexml_from)
    {

        if (!$simplexml_from instanceof SimpleXMLElement)
        {
            $simplexml_from = new SimpleXMLElement($simplexml_from);
        }

        foreach ($simplexml_from->children() as $simplexml_child)
        {
            $simplexml_temp = $simplexml_to->addChild($simplexml_child->getName(), (string) $simplexml_child);
            foreach ($simplexml_child->attributes() as $attr_key => $attr_value)
            {
                $simplexml_temp->addAttribute($attr_key, $attr_value);
            }

            $this->append_simplexml($simplexml_temp, $simplexml_child);
        }
    }
}