<?php
namespace Core\Library\Util;


class Password
{
    /**
     * Hash the password using the specified algorithm
     *
     * @param string $sPassword The password to hash
     * @param int $sAlgo The algorithm to use (Defined by PASSWORD_* constants)
     * @param array $aOptions The options for the algorithm to use
     *
     * @return string|false The hashed password, or false on error.
     */
    public function make($sPassword, $sAlgo = PASSWORD_DEFAULT, array $aOptions = array())
    {
        return password_hash($sPassword, $sAlgo, $aOptions);
    }

    /**
     * Get information about the password hash. Returns an array of the information
     * that was used to generate the password hash.
     *
     * array(
     *    'sAlgo' => 1,
     *    'algoName' => 'bcrypt',
     *    'options' => array(
     *        'cost' => 10,
     *    ),
     * )
     *
     * @param string $sHash The password hash to extract info from
     *
     * @return array The array of information about the hash.
     */
    public function getInfos($sHash)
    {
        return password_get_info($sHash);
    }

    /**
     * Determine if the password hash needs to be rehashed according to the options provided
     *
     * If the answer is true, after validating the password using password_verify, rehash it.
     *
     * @param string $sHash The hash to test
     * @param int $sAlgo The algorithm used for new password hashes
     * @param array $aOptions The options array passed to password_hash
     *
     * @return boolean True if the password needs to be rehashed.
     */

    public function needsRehash($sHash, $sAlgo = PASSWORD_DEFAULT, array $aOptions = array())
    {
        return password_needs_rehash($sHash, $sAlgo, $aOptions);
    }

    /**
     * Verify a password against a hash using a timing attack resistant approach
     *
     * @param string $sPassword The password to verify
     * @param string $sHash The hash to verify against
     *
     * @return boolean If the password matches the hash
     */
    public function verify($sPassword, $sHash)
    {
        return password_verify($sPassword, $sHash);
    }
}