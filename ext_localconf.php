<?php


use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\InfomaniakVod\Resource\OnlineMedia\Helpers\InfomaniakVodHelper;
use WapplerSystems\InfomaniakVod\Resource\Rendering\InfomaniakVodRenderer;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['infomaniakvod'] = InfomaniakVodHelper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',infomaniakvod';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['FileInfo']['fileExtensionToMimeType']['infomaniakvod'] = 'video/infomaniakvod';

$rendererRegistry = GeneralUtility::makeInstance(RendererRegistry::class);
$rendererRegistry->registerRendererClass(InfomaniakVodRenderer::class);
unset($rendererRegistry);
