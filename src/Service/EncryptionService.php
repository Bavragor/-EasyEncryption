<?php

namespace SecurityCompetition\Service;

use Defuse\Crypto\Crypto;

class EncryptionService
{
    /**
     * Encrypts a given resource and returns the encrypted binary as a string
     *
     * @param resource $resource
     * @param string $encryptionString
     * @return string
     *
     * @throws \Exception
     */
    public function encrypt($resource, $encryptionString)
    {
        if (is_resource($resource)) {
            return Crypto::encryptWithPassword(
                fread($resource, filesize(stream_get_meta_data($resource)['uri'])),
                $encryptionString,
                true
            );
        }

        throw new \Exception('No resource to encrypt given', 404);
    }
}
