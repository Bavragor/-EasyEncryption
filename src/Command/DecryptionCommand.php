<?php

namespace SecurityCompetition\Command;

use SecurityCompetition\Service\DecryptionService;
use SecurityCompetition\Service\ZipArchiveService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecryptionCommand extends Command
{
    const MODE_READ = 'r';
    const MODE_OVERWRITE = 'wa+';
    const ZIP_NAME = 'package.zip';
    const ENCRYPTED_NAME = 'encrypted';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DecryptionService
     */
    private $decryptionService;

    /**
     * @var ZipArchiveService
     */
    private $zipArchiveService;

    /**
     * @var string
     */
    private $encryptionKey;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->decryptionService = new DecryptionService();
        $this->zipArchiveService = new ZipArchiveService();
    }

    protected function configure()
    {
        $this
            ->setName('security:decrypt')
            ->setDescription('Decrypt stuff')
            ->addArgument(
                'package',
                InputArgument::REQUIRED,
                'Path to the encrypted package'
            )
            ->addArgument(
                'image',
                InputArgument::REQUIRED,
                'Image which will be used for decryption'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path were the decrypted package will be placed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $image = realpath($input->getArgument('image'));
        $package = realpath($input->getArgument('package'));
        $path = realpath($input->getArgument('path'));

        if (!is_readable($package) || !is_readable($image) || !is_writable($path)) {
            $output->writeln('<error>Boom</error>');

            return;
        }

        $imageResource = fopen($image, self::MODE_READ);

        $this->encryptionKey = base64_encode(fread($imageResource, filesize($image)));

        $this->handle($package, $path);
    }

    private function handle($encryptedPackage, $path)
    {
        $decryptedString = $this->decryptionService->decrypt(
            fopen($encryptedPackage, self::MODE_READ),
            $this->encryptionKey
        );

        $resource = fopen($path . DIRECTORY_SEPARATOR . self::ZIP_NAME, self::MODE_OVERWRITE);

        if (!fwrite($resource, $decryptedString)) {
            throw new \Exception('Decrypted file could not be written');
        }
    }
}
