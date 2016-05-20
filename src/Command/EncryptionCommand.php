<?php

namespace SecurityCompetition\Command;

use SecurityCompetition\Service\EncryptionService;
use SecurityCompetition\Service\ZipArchiveService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptionCommand extends Command
{
    /**
     * Mode for reading stuff
     */
    const MODE_READ = 'r';

    /**
     * Mode which will be used for writing
     */
    const MODE_OVERWRITE = 'wa+';

    /**
     * Name of the created archive
     */
    const ZIP_NAME = 'package.zip';

    /**
     * Name of the encrypted file
     */
    const ENCRYPTED_NAME = 'encrypted';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var EncryptionService
     */
    private $encryptionService;

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
        $this->encryptionService = new EncryptionService();
        $this->zipArchiveService = new ZipArchiveService();
    }

    protected function configure()
    {
        $this
            ->setName('security:encrypt')
            ->setDescription('Encrypt stuff')
            ->addArgument(
                'files',
                InputArgument::REQUIRED,
                'Either a path to a file or a directory, which will be encrypted'
            )
            ->addArgument(
                'image',
                InputArgument::REQUIRED,
                'Image which will be used for encryption'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path were the encrypted package will be created'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $files = realpath($input->getArgument('files'));
        $image = realpath($input->getArgument('image'));
        $path = realpath($input->getArgument('path'));

        if (!is_readable($files) || !is_readable($image) || !is_writable($path)) {
            $output->writeln('<error>Please set the right permissions</error>');

            return;
        }

        $imageResource = fopen($image, self::MODE_READ);

        $this->encryptionKey = base64_encode(fread($imageResource, filesize($image)));

        $this->handle($files, $path);
    }

    private function handle($source, $packagePath)
    {
        if ($this->zipArchiveService->createArchive($source, $packagePath . DIRECTORY_SEPARATOR . self::ZIP_NAME)) {
            $encryptedString = $this->encryptionService->encrypt(
                fopen($packagePath . DIRECTORY_SEPARATOR . self::ZIP_NAME, self::MODE_READ),
                $this->encryptionKey
            );

            $package = fopen($packagePath . DIRECTORY_SEPARATOR . self::ENCRYPTED_NAME, self::MODE_OVERWRITE);

            if (!fwrite($package, $encryptedString)) {
                throw new \Exception('Encrypted file could not be written');
            }
        }
    }
}
