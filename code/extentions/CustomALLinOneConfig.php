<?php

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\File;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\DataExtension;

class CustomALLinOneConfig extends DataExtension 
{

    private static $db = [
        'PageFooter'    => 'Text',
        'PageTop'       => 'Text',
        'CustomCssFile' => 'Varchar'
    ];

    private static $has_one = [
        'TopLogo' => Image::class
    ];
    private static $defaults = [
        'PageFooter'    => 'PageFooter in settings @ PageFooter 2030',
        'PageTop'       => '',
        'CustomCssFile' => 'custom.css'
    ];

    public function updateCMSFields(FieldList $fields) 
    {

        $fields->addFieldsToTab("Root.Main", [
            UploadField::create('TopLogo', 'Logo display at the top navbar'),
            HtmlEditorField::create('PageFooter', 'Bottom content of every page'),
            HtmlEditorField::create('PageTop', 'Content above navbar'),
            TextField::create('CustomCssFile', 'Custom CSS File'),
        ]);                       
    }
}