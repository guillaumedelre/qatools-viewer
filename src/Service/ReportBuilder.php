<?php

namespace App\Service;

use App\Model\Report;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class ReportBuilder implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    /** @var SessionInterface */
    protected $session;

    /** @var string */
    protected $kernelProjectDir;

    public function __construct(SessionInterface $session, string $kernelProjectDir)
    {
        $this->session = $session;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    public function build(string $vendor, string $action)
    {
        $dataView = new ParameterBag();

        $finder = (new Finder())
            ->in("{$this->kernelProjectDir}/var/build/qa")
            ->name($vendor . '*.svg')
        ;

        if ($finder->hasResults()) {
            try {
                /** @var Report $report */
                $report = $this->serializer->deserialize(
                    serialize($finder),
                    Report::class,
                    'svg',
                    [
                        'vendor' => $vendor,
                        'action' => $action,
                    ]
                );

                $dataView->set('graphs', $report);
            } catch (ExceptionInterface $e) {
                /** @var FlashBagInterface $flashBag */
                $flashBag = $this->session->getFlashBag();
                $flashBag->add(
                    'warning',
                    [
                        'heading' => $e->getMessage(),
                        'icon'    => 'fas fa-exclamation-circle',
                        'message' => $finder->files()->count(),
                        'trace'   => $e->getTraceAsString(),
                    ]
                );
            }
        }

        $finder = (new Finder())
            ->in("{$this->kernelProjectDir}/var/build/qa")
            ->notName($vendor . '*.svg')
            ->name($vendor . '*')
        ;

        /** @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            try {
                /** @var Report $report */
                $report = $this->serializer->deserialize(
                    $file->getContents(),
                    Report::class,
                    $file->getExtension(),
                    [
                        'file' => $file,
                        'vendor' => $vendor,
                        'action' => $action,
                    ]
                );

                $dataView->set($file->getFilenameWithoutExtension(), $report);
            } catch (ExceptionInterface $e) {
                /** @var FlashBagInterface $flashBag */
                $flashBag = $this->session->getFlashBag();
                $flashBag->add(
                    'warning',
                    [
                        'heading' => $e->getMessage(),
                        'icon'    => 'fas fa-exclamation-circle',
                        'message' => $file->getFilename(),
                        'trace'   => $e->getTraceAsString(),
                    ]
                );
            }
        }

        return $dataView;
    }
}
