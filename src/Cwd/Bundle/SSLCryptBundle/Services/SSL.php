<?php
/*
 * This file is part of password-manager.
 *
 * (c)2014 Ludwig Ruderstaller <lr@cwd.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cwd\Bundle\SSLCryptBundle\Services;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SSL
 *
 * @package Cwd\Bundle\SSLCryptBundle\Services
 * @author  Ludwig Ruderstaller <lr@cwd.at>
 * @DI\Service("cwd.bundle.sslcrypt.ssl")
 */
class SSL
{
    protected $config = array(
        'digest_alg'        => 'sha512',
        'private_key_bits'  => 4096,
        'private_key_type'  => OPENSSL_KEYTYPE_RSA,
        'encrypt_key'       => true,
    );

    protected $dn = array(
        'countryName' => 'AT',
        'stateOrProvinceName' => 'Vienna',
        'localityName' => 'Vienna',
        'organizationName' => 'Password Manager',
        'organizationalUnitName' => 'IT',
        'commonName' => 'myuser@host.at',
        'emailAddress' => 'myuser@host.at'
    );

    protected $publicKey;

    protected $secretKey;

    /**
     * Generate new private key
     *
     * @param string $password
     */
    public function generateKey($password)
    {
        $keypair = openssl_pkey_new($this->config);
        openssl_pkey_export($keypair, $privateKey, $password, $this->config);
        $this->secretKey = $privateKey;
        $pubKey = openssl_pkey_get_details($keypair);
        $this->publicKey = $pubKey['key'];
    }

    /**
     * @param string $encrypted
     * @param string $envKeys
     * @param string $privKey
     * @param string $password
     *
     * @return string
     * @throws \Exception
     */
    public function decrypt($encrypted, $envKeys, $privKey, $password)
    {
        $key = openssl_get_privatekey($privKey, $password);
        if (!$key) {
            throw new \Exception('PrivateKey Password is invalid');
        }

        foreach ($envKeys as $envKey) {
            openssl_open(base64_decode($encrypted), $data, base64_decode($envKey), $key);
            if ($data != null) {
                openssl_free_key($key);

                return $data;
            }
        }

        openssl_free_key($key);
        throw new \Exception('Unknown error: '.openssl_error_string());
    }

    /**
     * Encrypt data with given publicKeys
     *
     * @param string $data
     * @param array  $publicKeys
     *
     * @return array
     */
    public function encrypt($data, $publicKeys = array())
    {
        $keys = array();
        foreach ($publicKeys as $key) {
            $keys[] = openssl_get_publickey($key);
        }

        openssl_seal($data, $encryptedData, $envKey, $keys);

        foreach ($keys as $key) {
            openssl_free_key($key);
        }

        foreach ($envKey as $i => $k) {
            $envKey[$i] = base64_encode($k);
        }

        return array(
            'data'   => base64_encode($encryptedData),
            'envKey' => $envKey
        );
    }

    /**
     * Get Private Key
     *
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->secretKey;
    }

    /**
     * Get Public Key
     *
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}