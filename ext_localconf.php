<?php

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\InfomaniakVod\Resource\Rendering\InfomaniakVodRenderer;

$rendererRegistry = GeneralUtility::makeInstance(RendererRegistry::class);
$rendererRegistry->registerRendererClass(InfomaniakVodRenderer::class);
unset($rendererRegistry);


$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
$iconRegistry->registerFileExtension('infomaniakvod', 'mimetypes-media-video-infomaniakvod');
$iconRegistry->registerIcon(
    'mimetypes-media-video-infomaniakvod',
    BitmapIconProvider::class,
    ['source' => 'EXT:infomaniak-vod/Resources/Public/Icons/infomaniakvod.png']
);


$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['vod'] = \WapplerSystems\InfomaniakVod\Resource\OnlineMedia\Helpers\InfomaniakVodHelper::class;
