<?php
namespace SilverstripeALLinOne\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class CustomMainPage extends DataExtension
{
    private static $db = [
        'FeaturedText' => 'Text',
        'FeaturedContent' => 'HTMLText',
    ];

    private static $has_one = [
        'BannerImage' => Image::class,
        'FeaturedImage' => Image::class
    ];

    private static $defaults = [];

    public function updateCMSFields(FieldList $fields)
    {

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('FeaturedText', 'Featured Text'),
            UploadField::create('FeaturedImage', 'Featured Image'),
            
            HtmlEditorField::create('FeaturedContent', 'Featured Content'),
            UploadField::create('BannerImage', 'Banner Image'),
        ]);
        parent::updateCMSFields($fields);
    }

}