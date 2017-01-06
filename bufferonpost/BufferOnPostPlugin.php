<?php
namespace Craft;

class BufferOnPostPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('BufferOnPost');
    }

    function getVersion()
    {
        return '1.0.0';
    }

    function getDeveloper()
    {
        return 'samleblanc';
    }

    function getDeveloperUrl()
    {
        return 'http://mainmenu.reviews';
    }

    public function hasCpSection()
    {
        return false;
    }

    public function init()
    {
        parent::init();

        craft()->on('entries.onBeforeSaveEntry', function(Event $event) 
        {
            $entry = $event->params['entry'];
            // For posting entries only from a particular section
            if ($entry->sectionId == ENTRY_SECTION_ID) 
            {
                $siteUrl = craft()->siteUrl;
                $hashTag = " " . "#YOUR_HASHTAG";
                $userName = " " . "YOUR USERNAME";

                $updateLink = $siteUrl . "SECTION_PATH/" . $entry->slug;

                // build our string to be posted
                $updateString = "New Post: “" . $entry->title . "” by" . $userName . " " . $hashTag . " " . $updateLink;
                
                // expects a 'featuredImage' field, and a 'thumbnail' image transformation
                $updateImage = $siteUrl . $entry->featuredImage[0]->url;
                $updateThumb = $siteUrl . $entry->featuredImage[0]->getUrl('thumbnail');

                // In this case, we check against an expected lightswitch field called
                // 'socialOnSave', which allows for more control over when the 
                // post is sent to buffer
                if($entry->socialOnSave){
                    craft()->buffer_utils->sendBufferUpdate($updateString, false, $updateLink, $updateImage, $updateThumb);
                    // Reset socialOnSave to false
                    $entry->getContent()->setAttributes(array(
                        'socialOnSave' => 0
                    ));
                } else {
                    // Do nothing
                }
            }
        });
    }

}