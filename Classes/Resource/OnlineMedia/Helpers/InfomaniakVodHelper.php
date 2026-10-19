<?php

namespace WapplerSystems\InfomaniakVod\Resource\OnlineMedia\Helpers;

use TYPO3\CMS\Core\Resource\Exception\OnlineMediaAlreadyExistsException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 */
class InfomaniakVodHelper extends AbstractOEmbedHelper
{
    /**
     * Get public url
     *
     * @return string|null
     */
    public function getPublicUrl(File $file)
    {
        $videoId = $this->getOnlineMediaId($file);
        return sprintf('https://player.vod2.infomaniak.com/embed/%s', rawurlencode($videoId));
    }

    /**
     * Get local absolute file path to preview image
     *
     * @return string
     */
    public function getPreviewImage(File $file)
    {

        $videoId = $this->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . 'infomaniakvod_' . md5($videoId) . '.jpg';

        if (!file_exists($temporaryFileName)) {
            // The old ".preload.jpeg" share URL returns 404, the share data names the current image URLs
            $oEmbed = $this->getOEmbedData($videoId);
            $tryUrls = array_filter([
                $oEmbed['data']['media'][0]['thumbnails']['poster'] ?? '',
                $oEmbed['data']['player']['preload_image'] ?? '',
                sprintf('https://api.infomaniak.com/2/vod/res/shares/%s.preload.jfif', rawurlencode($videoId)),
            ]);
            foreach ($tryUrls as $tryUrl) {
                $previewImage = GeneralUtility::getUrl($tryUrl);
                if ($previewImage !== false && $previewImage !== '') {
                    GeneralUtility::writeFile($temporaryFileName, $previewImage, true);
                    break;
                }
            }
        }

        return $temporaryFileName;
    }

    /**
     * Try to transform given URL to a File
     *
     * @param string $url
     * @return File|null
     */
    public function transformUrlToFile($url, Folder $targetFolder)
    {

        $videoId = null;
        // Try to get the YouTube code from given url.
        // These formats are supported with and without http(s)://
        // - player.vod2.infomaniak.com/share/12345678
        if (preg_match('%https?://player\.vod2\.infomaniak\.com/share/([^/?#]+)%i', $url, $matches)) {
            $videoId = $matches[1];
        }
        if (empty($videoId)) {
            return null;
        }
        return $this->transformMediaIdToFile($videoId, $targetFolder, $this->extension);
    }

    /**
     * Get oEmbed url to retrieve oEmbed data
     *
     * @param string $mediaId
     * @param string $format
     * @return string
     */
    protected function getOEmbedUrl($mediaId, $format = 'json')
    {
        return sprintf('https://api.infomaniak.com/2/vod/res/shares/%s.json',rawurlencode($mediaId));
    }

    /**
     * Transform mediaId to File
     *
     * @param string $mediaId
     * @param string $fileExtension
     * @return File
     */
    protected function transformMediaIdToFile($mediaId, Folder $targetFolder, $fileExtension)
    {
        $file = $this->findExistingFileByOnlineMediaId($mediaId, $targetFolder, $fileExtension);
        if ($file !== null) {
            throw new OnlineMediaAlreadyExistsException($file, 1695236851);
        }
        // no existing file create new
        $oEmbed = $this->getOEmbedData($mediaId);

        if (!empty($oEmbed['data']['media'][0]['title'] ?? '')) {
            $fileName = $oEmbed['data']['media'][0]['title'] . '.' . $fileExtension;
        } else {
            $fileName = $mediaId . '.' . $fileExtension;
        }
        return $this->createNewFile($targetFolder, $fileName, $mediaId);
    }


    public function getMetaData(File $file)
    {
        $metadata = [];

        $oEmbed = $this->getOEmbedData($this->getOnlineMediaId($file));
        // The share data has no oEmbed format: the player size and the media title are nested
        $metadata['width'] = (int)($oEmbed['data']['player']['width'] ?? 0);
        $metadata['height'] = (int)($oEmbed['data']['player']['height'] ?? 0);

        if (empty($file->getProperty('title'))) {
            $metadata['title'] = strip_tags($oEmbed['data']['media'][0]['title'] ?? '');
        }

        return $metadata;
    }

}
