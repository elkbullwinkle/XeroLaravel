<?php
/**
 * Created by PhpStorm.
 * User: elkbullwinkle
 * Date: 23/12/16
 * Time: 6:07 PM
 */

namespace Elkbullwinkle\XeroLaravel\Models\Accounting;


use Elkbullwinkle\XeroLaravel\Models\XeroModel;

class BrandingTheme extends XeroModel
{
    protected $pageable = false;

    protected $cat = 'accounting';

    protected $endpoint = 'BrandingThemes';

    protected $id = 'BrandingThemeID';

    protected $required = [
    ];

    protected $attrs = [

        //Read only retrieved on GET requests

        'BrandingThemeID' => 'guid',

        'Name' => 'string',

        'SortOrder' => 'int',

        'CreatedDateUTC' => 'net-date',

    ];
}