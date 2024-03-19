<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Nitsan\NsHubSpot\Finishers;

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use GuzzleHttp\Client;

/**
 * This finisher sends an email to one recipient
 *
 * Options:
 *
 * - templateName (mandatory): Template name for the mail body
 * - templateRootPaths: root paths for the templates
 * - layoutRootPaths: root paths for the layouts
 * - partialRootPaths: root paths for the partials
 * - variables: associative array of variables which are available inside the Fluid template
 *
 * The following options control the mail sending. In all of them, placeholders in the form
 * of {...} are replaced with the corresponding form value; i.e. {email} as senderAddress
 * makes the recipient address configurable.
 *
 * - subject (mandatory): Subject of the email
 * - recipients (mandatory): Email addresses and human-readable names of the recipients
 * - senderAddress (mandatory): Email address of the sender
 * - senderName: Human-readable name of the sender
 * - replyToRecipients: Email addresses and human-readable names of the reply-to recipients
 * - carbonCopyRecipients: Email addresses and human-readable names of the copy recipients
 * - blindCarbonCopyRecipients: Email addresses and human-readable names of the blind copy recipients
 * - title: The title of the email - If not set "subject" is used by default
 *
 * Scope: frontend
 */
class ApiFinisher extends AbstractFinisher
{

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $renderingOptions = $formRuntime->getFormDefinition()->getRenderingOptions();
        $enableHubSpot = $renderingOptions['hubspotEnable'];
        $hubspotPortalId = $renderingOptions['hubspotPortalId'];
        $hubspotFormId = $renderingOptions['hubspotFormId'];
        $matchingFormValues = [];
        $folderName = '';
        $changeFormat = [];

        if($enableHubSpot == 'TRUE'){

            // get configuration from extension manager
            $constant = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_nshubspot.']['settings.'];
            $availablefileds = $formRuntime->getFormState()->getFormValues();
            $resultExtensProperties = [];

            foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
                if($element->getType() != 'Page' && $element->getType() != 'GridRow' && $element->getType() != 'Fieldset' && $element->getType() != 'Checkbox' && $element->getType() != 'StaticText' && $element->getType() != 'Recaptcha' && $element->getType() != 'Honeypot'){
                    foreach($element->getRenderingOptions() as $key => $newProperties){

                        if($key == 'hubSpotValue'){

                            if(is_string($newProperties)){
                                $resultExtensProperties[] = $newProperties;
                            }

                            if (!$element instanceof FileUpload) {
                                continue;
                            }
                            $file = $formRuntime[$element->getIdentifier()];

                            if (!$file) {
                                continue;
                            }

                            if ($file instanceof FileReference) {
                                $file = $file->getOriginalResource();
                            }

                            $folder = $file->getParentFolder();
                            $folderName = $folder->getName();
                            $fileName = $file->getName();

                        }
                    }
                    foreach($availablefileds as $key => $values){

                        if($key == $element->getIdentifier()){
                            if(is_array($values)){
                                $values = implode(', ', $values);
                            }
                            $matchingFormValues[$key] = $values;
                        }
                    }
                }
            }

            $finalResult = array_combine($resultExtensProperties, $matchingFormValues);
            $fileName = isset($fileName) ? $fileName : "default";

            if(!empty($fileName)){
                $replaceImgValue = array("file_upload" => $fileName);
                $finalResult = array_replace($finalResult,$replaceImgValue);

                $filename = $folderName . '/' . $finalResult['file_upload'];

                $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
                $fileStorage = $resourceFactory->getDefaultStorage();
                $folder = $fileStorage->getFolder('/user_upload');
                $fileObject = $fileStorage->getFileInFolder($filename, $folder);
                $accessibleUrl = $fileObject->getPublicUrl();

                $replaceImgWithAbsoulte = array("file_upload" => $accessibleUrl);
                $finalResult = array_replace($finalResult,$replaceImgWithAbsoulte);
            }

            foreach ($finalResult as $key => $value){
                if(!empty($value)){
                    $changeFormat[] = [
                        'name' => $key,
                        'value' => $value
                    ];
                }
            }
            $data['fields'] = $changeFormat;

            // HubSpot API Key
            $apiKey = $constant['apiURL'];
            $clientAPIKey = $constant['client_apikey'];

            // Guzzle HTTP client
            $client = new Client([
                'base_uri' => $apiKey,
                'timeout'  => 2.0,
            ]);

            $apiURL = $apiKey . '/submissions/v3/integration/submit/' . $hubspotPortalId . '/' . $hubspotFormId;
            // Submit form data
            $client->request('POST', $apiURL, [
                'query' => ['hapikey' => $clientAPIKey],
                'json' => $data
            ]);

        }
    }
}
