<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Forms;
use App\Model;


class ImagesPresenter extends BasePresenter
{

	public function renderImages()
	{
        // Authentication
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }

        // List all content from the Azure Storage Container
        $images = [];
        $blob_list = $this->azureBlobStorage->listBlobs("mycontainer");
        $blobs = $blob_list->getBlobs();

        foreach($blobs as $blob)
        {
            $images[] = [
                'name' => $blob->getName(),
                'url' => $blob->getUrl()
            ];
        }
        // Pass it into template
        $this->template->images = $images;
	}

    protected function createComponentUploadImgForm() {
        $form = new UI\Form();
        $presenter = $this;
        $form->addGroup()
                ->setOption('container', \Nette\Utils\Html::el('td')->id('newPic'));
        $form->addUpload('img', 'Image:')
                //->addRule(Form::IMAGE, 'Only .jpg, .gif, .png allowed!')
                ->addRule(Form::MAX_FILE_SIZE, 'File is too large, max 2MB.', 2 * 1024 * 1024);
        //$form->addText('desc', 'Description:');



        $form->addSubmit('save', 'Save')->setAttribute('class', 'default');
        $form->addSubmit('cancel', 'Cancel')
                ->setValidationScope(FALSE)
                ->onClick[] = function () use ($presenter) {
                    $presenter->redirect('Images:images');
                };



        $form->onSuccess[] = [$this, 'actionUploadImgFormSubmitted'];

        return $form;
    }

    public function actionUploadImgFormSubmitted(UI\Form $form, $values)
    {
        // Get form data
        $file = $form['img']->getValue();
        // Open file for reading, file_get_contents can be used too
        $content = fopen($file, 'r');
        $blob_name = $file->name;

        // Upload the file to Azure Storage
        $this->azureBlobStorage->createBlockBlob("mycontainer", $blob_name, $content);

        // Close the read operation and delete from Temp
        fclose($content);
        unlink($file);
    }

}
