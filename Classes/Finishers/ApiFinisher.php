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

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;

/**
 * This finisher sends form data to HubSpot.
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
        $enableHubSpot = $renderingOptions['hubspotEnable'] ?? false;
        $hubspotPortalId = (string)($renderingOptions['hubspotPortalId'] ?? '');
        $hubspotFormId = (string)($renderingOptions['hubspotFormId'] ?? '');

        if (!$this->isHubSpotEnabled($enableHubSpot)) {
            return;
        }

        $constant = $this->resolveHubSpotConfiguration();
        $clientAPIKey = trim((string)($constant['client_apikey'] ?? ''));
        $apiBaseUrl = rtrim((string)($constant['apiURL'] ?? 'https://api.hsforms.com'), '/');

        if ($clientAPIKey === '' || $hubspotPortalId === '' || $hubspotFormId === '') {
            return;
        }

        $availableFields = $formRuntime->getFormState()->getFormValues();
        $finalResult = [];
        $folderName = '';
        $fileName = '';

        foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
            if (in_array($element->getType(), ['Page', 'GridRow', 'Fieldset', 'Checkbox', 'StaticText', 'Recaptcha', 'Honeypot'], true)) {
                continue;
            }

            $identifier = $element->getIdentifier();
            $hubSpotValue = $element->getRenderingOptions()['hubSpotValue'] ?? $element->getProperties()['hubSpotValue'] ?? null;

            if (is_string($hubSpotValue) && $hubSpotValue !== '' && array_key_exists($identifier, $availableFields)) {
                $value = $availableFields[$identifier];
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                if ($value !== null && $value !== '') {
                    $finalResult[trim($hubSpotValue)] = (string)$value;
                }
            }

            if ($hubSpotValue !== null && $element instanceof FileUpload) {
                $file = $formRuntime[$identifier];
                if ($file) {
                    if ($file instanceof FileReference) {
                        $file = $file->getOriginalResource();
                    }
                    $folder = $file->getParentFolder();
                    $folderName = $folder->getName();
                    $fileName = $file->getName();
                }
            }
        }

        if ($fileName !== '' && $folderName !== '') {
            try {
                $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
                $fileIdentifier = '1:/user_upload/' . $folderName . '/' . $fileName;
                $fileObject = $resourceFactory->getFileObjectFromCombinedIdentifier($fileIdentifier);
                $accessibleUrl = $fileObject->getPublicUrl();
                $finalResult['file_upload'] = $accessibleUrl;
            } catch (\Exception $e) {
                $finalResult['file_upload'] = '';
            }
        }

        $changeFormat = [];
        foreach ($finalResult as $key => $value) {
            if ($value !== '') {
                $changeFormat[] = [
                    'name' => $key,
                    'value' => $value,
                ];
            }
        }

        if ($changeFormat === []) {
            return;
        }

        $isPrivateAppToken = str_starts_with($clientAPIKey, 'pat-');
        $submitPath = $isPrivateAppToken
            ? '/submissions/v3/integration/secure/submit/'
            : '/submissions/v3/integration/submit/';
        $apiURL = $apiBaseUrl . $submitPath . $hubspotPortalId . '/' . $hubspotFormId;

        $requestOptions = [
            'json' => ['fields' => $changeFormat],
        ];
        if ($isPrivateAppToken) {
            $requestOptions['headers'] = [
                'Authorization' => 'Bearer ' . $clientAPIKey,
            ];
        } else {
            $requestOptions['query'] = ['hapikey' => $clientAPIKey];
        }

        $client = new Client([
            'base_uri' => $apiBaseUrl,
            'timeout' => 10.0,
        ]);
        $client->request('POST', $apiURL, $requestOptions);
    }

    private function isHubSpotEnabled(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    private function resolveHubSpotConfiguration(): array
    {
        $config = [
            'apiURL' => 'https://api.hsforms.com',
            'client_apikey' => '',
            'client_id' => '',
        ];

        $site = $GLOBALS['TYPO3_REQUEST']?->getAttribute('site');
        if ($site) {
            $siteHubspot = $site->getSettings()->get('nshubspot', []);
            if (is_array($siteHubspot)) {
                foreach ($siteHubspot as $key => $value) {
                    if (is_string($value) && $value !== '') {
                        $config[$key] = $value;
                    }
                }
            }
        }

        $fullSetup = $GLOBALS['TYPO3_REQUEST']?->getAttribute('frontend.typoscript')?->getSetupArray();
        $tsHubspot = $fullSetup['plugin.']['tx_nshubspot.']['settings.'] ?? [];
        if (is_array($tsHubspot)) {
            foreach ($tsHubspot as $key => $value) {
                if (is_string($value) && $value !== '') {
                    $config[$key] = $value;
                }
            }
        }

        return $config;
    }
}
