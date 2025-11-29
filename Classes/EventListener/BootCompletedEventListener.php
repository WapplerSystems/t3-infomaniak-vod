<?php


use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(public: true)]
class BootCompletedEventListener {

    #[AsEventListener('infomaniak-vod/boot-completed')]
    public function bootCompleted(BootCompletedEvent $e): void
    {
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        $iconRegistry->registerFileExtension('infomaniakvod', 'mimetypes-media-video-infomaniakvod');
        $iconRegistry->registerIcon(
            'mimetypes-media-video-infomaniakvod',
            BitmapIconProvider::class,
            ['source' => 'EXT:infomaniak_vod/Resources/Public/Icons/infomaniakvod.png']
        );
    }
}
