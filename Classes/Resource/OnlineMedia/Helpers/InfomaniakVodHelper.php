<?php

namespace WapplerSystems\InfomaniakVod\Resource\OnlineMedia\Helpers;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOEmbedHelper;
use TYPO3\CMS\Core\Utility\DebugUtility;
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
        return sprintf('https://www.youtube.com/watch?v=%s', rawurlencode($videoId));
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
            $previewImage = GeneralUtility::getUrl(
                sprintf('https://api.vod2.infomaniak.com/2/vod/res/shares/%s.preload.jpeg', $videoId)
            );
            if ($previewImage !== false) {
                GeneralUtility::writeFile($temporaryFileName, $previewImage, true);
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
        return sprintf(
            'https://www.youtube.com/oembed?url=%s&format=%s&maxwidth=2048&maxheight=2048',
            rawurlencode(sprintf('https://www.youtube.com/watch?v=%s', rawurlencode($mediaId))),
            rawurlencode($format)
        );
    }
}
