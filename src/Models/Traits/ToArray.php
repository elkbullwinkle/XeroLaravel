<?php

namespace Elkbullwinkle\XeroLaravel\Models\Traits;

use Carbon\Carbon;
use Elkbullwinkle\XeroLaravel\Models\XeroModel;
use Illuminate\Support\Collection;

trait ToArray {

    /**
     * Export Xero model to array
     *
     * @return array
     */
    public function toArray()
    {

        $arr = [];

        foreach ($this->attributes as $name => $attribute)
        {
            if (is_subclass_of($attribute, XeroModel::class))
            {
                $arr[$name] = $attribute->toArray();
            }
            elseif($attribute instanceof Carbon)
            {
                $arr[$name] = $attribute->toDateTimeString();
            }
            elseif($attribute instanceof Collection)
            {
                $arr[$name] = $attribute->map(function($item) {
                    return $item->toArray();
                })->all();
            }
            else
            {
                $arr[$name] = $attribute;
            }
        }

        return $arr;
    }

}