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
use Symfony\Component\Yaml\Yaml;

class CustomALLinOneConfig extends DataExtension 
{

    private static $db = [
        'PageFooter'    => 'HTMLText',
        'PageTop'       => 'HTMLText',
        'CustomCssFile' => 'Varchar',
        'CustomThemeName' => 'Varchar'
    ];

    private static $has_one = [
        'TopLogo' => Image::class
    ];
    private static $owns = ['TopLogo'];


    private static $defaults = [
        'PageFooter'    => '<p class="text-center">&nbsp;</p><p class="text-center">PageFooter in settings @ PageFooter 2030</p><p class="text-center">&nbsp;</p>',
        'PageTop'       => '',
        'CustomCssFile' => 'custom.css',
        'CustomThemeName' => 'silverstripe-custom-bootstrap4-theme'
    ];

    public function updateCMSFields(FieldList $fields) 
    {
        $themeList = array_map('basename', glob(BASE_PATH . "/themes/*", GLOB_ONLYDIR));
        foreach ($themeList as $k=>$v) {
            $newThemeList[$v] = $v;
        }

        $fields->addFieldsToTab("Root.Main", [

            DropdownField::create('CustomThemeName', 'Choose a Theme', $newThemeList)
                ->setDescription('Save it then you can <a href="/?flush=1" target="_blank">click here to see the changes in the homepage</a>'),   
                  
            
            UploadField::create('TopLogo', 'Logo display at the top navbar'),
            HtmlEditorField::create('PageFooter', 'Bottom content of every page'),
            HtmlEditorField::create('PageTop', 'Content above navbar'),
            TextField::create('CustomCssFile', 'Custom CSS File')
                ->setDescription('Please do not input .css, eg. custom.css, just input custom. main.css => main , xxx.css => xxx<br/>And this xxx.css should be located at your-ss-project/public/_resource/themes/your-theme/css/xxx.css')
        ]);
    }

    protected function changeTheme()
    {
        if ( isset($_POST['CustomThemeName']) && trim($_POST['CustomThemeName'])!='' ){

            $themeFile = BASE_PATH . '/app/_config/theme.yml';
            if ( !file_exists($themeFile) ){
                $themeFile = BASE_PATH . '/mysite/_config/theme.yml';
                if ( !file_exists($themeFile) ){
                    echo '<h2>Please make sure ' . BASE_PATH . '/app/_config/theme.yml exists</h2>';
                }                
            }
            $themeStr = file_get_contents($themeFile);

            // Symfony\Component\Yaml\Yaml not support ---
            // Replace it to something else first
            $themeStr = preg_replace_callback(
                "/\-\-\-/",
                function($m) {
                    static $id = 0;
                    $id++;
                    return 'ThreeDashes' . $id.': dashes';
                },
                $themeStr);


            $themeData = Yaml::parse($themeStr);

            $needUpdateThemeYml = false;


            // Update the first theme not contain $
            for($i=0; $i< count($themeData['SilverStripe\View\SSViewer']['themes']); $i++ ){
                if ( strpos($themeData['SilverStripe\View\SSViewer']['themes'][$i],'$') === false && $themeData['SilverStripe\View\SSViewer']['themes'][$i] != trim($_POST['CustomThemeName']) ){
                    $themeData['SilverStripe\View\SSViewer']['themes'][$i] = trim($_POST['CustomThemeName']);
                    $needUpdateThemeYml = true;
                    break;
                }
            }
            // Newly installed ss site with empty template
            if ( $needUpdateThemeYml == false && count($themeData['SilverStripe\View\SSViewer']['themes']) == 2 && $themeData['SilverStripe\View\SSViewer']['themes'][0] == '$public'  && $themeData['SilverStripe\View\SSViewer']['themes'][1] == '$default' ){
                $themeData['SilverStripe\View\SSViewer']['themes'][1] = trim($_POST['CustomThemeName']);
                $themeData['SilverStripe\View\SSViewer']['themes'][2] = '$default';
                $needUpdateThemeYml = true;
            }

            if ( $needUpdateThemeYml ){
                $newYaml = Yaml::dump($themeData,5,2);

                $newYaml = preg_replace("/ThreeDashes(\d): dashes/", "---", $newYaml);
                
                file_put_contents($themeFile, $newYaml);            

                // refresh the theme for homepage
                echo '<img src="/?flush=1" width=0 height=0 />'; 
            } else {
                echo '<!-- No needUpdateThemeYml -->';
            }

        } else {
            echo '<!-- CustomThemeName not set -->';
        }

    }

    public function onAfterWrite() 
    {
       $this->changeTheme("silverstripe-custom-bootstrap4-theme");

        // CAUTION: You are required to call the parent-function, otherwise
        // SilverStripe will not execute the request.
        parent::onAfterWrite();
    }
}