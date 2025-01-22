<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class Jwt
{
    protected $token = null;
    public string $storageKey = 'token';

    public function __construct()
    {

    }

    /**
     * @param string $jwt The JWT
     * @param bool $verify Don't skip verification process
     *
     * @return object The JWT's payload as a PHP object
     * @throws JwtException
     */
    public function decode(string $jwt, bool $verify = true): object
    {
        $key = config('app.key');
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new JwtException('Wrong number of segments');
        }
        list($headb64, $payloadb64, $cryptob64) = $tks;
        if (($header = json_decode(self::urlsafeB64Decode($headb64))) === null) {
            throw new JwtException('Invalid header segment encoding');
        }
        if (($payload = json_decode(self::urlsafeB64Decode($payloadb64))) === null) {
            throw new JwtException('Invalid payload segment encoding');
        }
        $sig = self::urlsafeB64Decode($cryptob64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new JwtException('Empty algorithm');
            }
            if ($sig != hash_hmac($header->alg, "$headb64.$payloadb64", $key, true)) {
                throw new JwtException('Signature verification failed');
            }
        }
        return $payload;
    }

    /**
     * @param object|array $payload PHP object or array
     * @param string $key The secret key
     * @param string $algo The signing algorithm
     *
     * @return string A JWT
     */
    public function encode(object|array $payload, string $algo = 'sha256'): string
    {
        $key = config('app.key');
        $header = array('typ' => 'JWT', 'alg' => $algo);

        $segments = array();
        $segments[] = self::urlsafeB64Encode(json_encode($header));
        $segments[] = self::urlsafeB64Encode(json_encode($payload));
        $signing_input = implode('.', $segments);

        $signature = hash_hmac($algo, $signing_input, $key, true);
        $segments[] = self::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    public static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    public function getTokenFromRequest(Request $request)
    {
        $token = $request->query($this->storageKey);
        if ($token) {
            logger()->info('token retrieved by query');
        }

        if (empty($token)) {
            $token = $request->input($this->storageKey);
            if ($token) {
                logger()->info('token retrieved by payload');
            }
        }

        if (empty($token)) {
            $token = $request->cookie($this->storageKey);
            if ($token) {
                logger()->info('token retrieved by cookie');
            }
        }

        if (empty($token)) {
            $token = $request->bearerToken();
            if ($token) {
                logger()->info('token retrieved by bearer header');
            }
        }

        return $token;
    }

    public function fromUser(Authenticatable $user): string
    {
        return $this->encode([
            'sub' => $user->getAuthIdentifier(),
            'email' => $user->email,
            //'password' => $user->getAuthPassword()
        ]);
    }

    public function setToken($token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @throws JwtException
     */
    protected function requireToken(): void
    {
        if (!$this->token) {
            throw new JwtException('A token is required');
        }
    }

    /**
     * @throws JwtException
     */
    public function getPayload($verify = true)
    {
        $this->requireToken();

        return $this->decode($this->token, $verify);
    }

    public function isTokenValid() {

        // тут реализация проверки токена на валидность в зависимости от механизма инвалидации

        return true;
    }

    /**
     * @throws JwtException
     */
    public function invalidate()
    {
        $this->requireToken();

        // тут реализация инвалидации токена

        return $this;
    }

}
