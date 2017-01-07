<?php
namespace Craft;

class BufferOnPost_ShareController extends BaseController {

    public function actionShare() {

        $id = craft()->request->getParam('id');

        // If no ID specified
        if($id === null) {
            return $this->redirect(rtrim('/', craft()->getSiteUrl())."/admin/dashboard?msg=share+error+no+id");
        }

        // Get the Element based on the provided ID
        $target = craft()->elements->getCriteria(ElementType::Entry)->find(['id'=>$id]);

        if(!count($target)) {
            return $this->redirect(rtrim('/', craft()->getSiteUrl())."/admin/dashboard?msg=share+error+invalid+id");
        }

        // Assume the first one, since we're searching by ID
        $entry = $target[0];

        $siteUrl = craft()->siteUrl;
        $hashTag = " " . "#YOUR_HASHTAG";
        $userName = " " . "YOUR USERNAME";

        $updateLink = $entry->getUrl();

        // build our string to be posted
        $updateString = "New Post: “" . $entry->title . "” by" . $userName . " " . $hashTag . " " . $updateLink;
        
        // expects a 'featuredImage' field, and a 'thumbnail' image transformation
        $updateImage = $siteUrl . $entry->featuredImage[0]->url;
        $updateThumb = $siteUrl . $entry->featuredImage[0]->getUrl('thumbnail');

        craft()->buffer_utils->sendBufferUpdate($updateString, false, $updateLink, $updateImage, $updateThumb);
        // Reset socialOnSave to false
        $entry->getContent()->setAttributes(array(
            'socialOnSave' => 0
        ));
        
        return $this->redirect($entry->getCpEditUrl()."?shared=1");
    }

}
