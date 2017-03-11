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
     * @param string $action Either creating or updating the model
     * @param SimpleXMLElement|null $parent Parent node to build complex nested structures
     * @param boolean $wrap Wrap root element with plural endpoint tag
     * @return string
     */
    protected function toXml($action = 'update', SimpleXMLElement &$parent = null, $wrap = false)
    {

        //Generating root element

        $singularEndpoint = str_singular($this->endpoint);

        //var_dump(['Converting =>' => $this->attributes]);

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

            if(!$this->getModelAttributeNeedsPosting($name, strtolower($action) == 'update'))
            {
                continue;
            }

            if (is_subclass_of($attribute, XeroModel::class))
            {

                $childXml = $rootXml->addChild(str_singular($name));

                $attribute->toXml($action, $childXml);

            }
            elseif($attribute instanceof Carbon)
            {

                $rootXml->addChild($name, $attribute->toDateTimeString());

            }
            elseif($attribute instanceof Collection)
            {

                $collection = $rootXml->addChild($name);

                $attribute->map(function($item) use ($collection, $name, $action) {


                    $childName = is_null($item->getOverriddenName()) ? str_singular($name) : $item->getOverriddenName();

                    $childXml = $collection->addChild($childName);

                    $item->toXml($action, $childXml);
                });
            }
            elseif(is_array($attribute))
            {
                $collection = $rootXml->addChild($name);

                foreach ($attribute as $subName => $sub)
                {
                    if (is_array($sub))
                    {
                        //var_dump($sub);
                        continue;
                    }

                    $collection->addChild($subName, htmlspecialchars($sub));

                }

            }
            else
            {
                //Checking for boolean value to manually pass

                if ($this->getModelAttributeType($name) == 'boolean')
                {
                    $rootXml->addChild($name, $attribute ? 'true' : 'false');
                }
                else
                {
                    if ($this->getModelAttributeType($name) == 'string')
                    {
                        $rootXml->addChild($name, htmlspecialchars($attribute));
                    }
                    else
                    {
                        $rootXml->addChild($name, $attribute);
                    }
                }

            }
        }

        return $wrap ? $_rootXml->asXML() : $rootXml->asXML();
    }


    /**
     * Determine if the attribute should be included to the server request
     *
     * @param string $attributeName Attribute name
     * @param bool $postGuid Whether model UUID should be posted
     * @return bool
     */
    protected function getModelAttributeNeedsPosting($attributeName, $postGuid = false)
    {
        if (is_null($attribute = $this->getModelAttribute($attributeName)))
        {
            return false;
        }

        return in_array('post', $attribute) || ($postGuid && $attribute['type'] == 'guid');
    }

}