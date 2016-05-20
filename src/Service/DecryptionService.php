<?php

namespace SecurityCompetition\Service;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class DecryptionService
{
    /**
     * Decrypts a given resource and returns the decrypted binary as a string
     *
     * @param resource $resource
     * @param string $encryptionString
     * @return string
     *
     * @throws \Exception
     */
    public function decrypt($resource, $encryptionString)
    {
        if (is_resource($resource)) {
            try {
                $decrypted = Crypto::decryptWithPassword(
                    fread($resource, filesize(stream_get_meta_data($resource)['uri'])),
                    $encryptionString,
                    true
                );
            } catch (WrongKeyOrModifiedCiphertextException $exception) {
                throw new \Exception('Could not decrypt, given key was wrong!', 500);
            }

            return $decrypted;
        }

        throw new \Exception('No resource to decrypt given', 404);
    }
}
