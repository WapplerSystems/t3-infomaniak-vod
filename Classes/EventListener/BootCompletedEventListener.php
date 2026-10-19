<?php

declare(strict_types=1);

namespace WapplerSystems\InfomaniakVod\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Imaging\IconRegistry;

/**
 * The file extension mapping is not cached by the IconRegistry, so it has to be set on every boot
 */
final readonly class BootCompletedEventListener
{
    public function __construct(private IconRegistry $iconRegistry) {}

    #[AsEventListener('infomaniak-vod/boot-completed')]
    public function __invoke(BootCompletedEvent $event): void
    {
        $this->iconRegistry->registerFileExtension('infomaniakvod', 'mimetypes-media-video-infomaniakvod');
    }
}
