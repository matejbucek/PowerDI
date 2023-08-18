<?php

namespace SimpleFW\Security\OAuth;

use SimpleFW\Annotations\Autowired;
use SimpleFW\HttpBasics\HttpRequest;
use SimpleFW\Logging\Logger;
use SimpleFW\Security\Exceptions\JwtBinderException;
use SimpleFW\Security\Principal;
use SimpleFW\Security\UserDataBinder;

class JwtUserBinder implements UserDataBinder {
    private string $authServerUrl;

    private string $header;
    #[Autowired("@Logger")]
    private Logger $logger;

    public function __construct(string $authServerUrl, string $header = "Authorization") {
        $this->authServerUrl = $authServerUrl;
        $this->header = $header;
    }

    public function getUser(HttpRequest $request): ?Principal {
        $authHeader = $request->getHeader($this->header);
        if ($authHeader == null)
            return null;

        $headerParts = explode(".", str_replace("/Bearer /", "", $authHeader));

        $jwtHeader = json_decode(base64_decode($headerParts[0]), true);
        $jwtPayload = json_decode(base64_decode($headerParts[1]), true);
        $jwtVerifySignature = $headerParts[2];

        if (isset($jwtHeader["typ"]) && !strcasecmp($jwtHeader["typ"], "JWT"))
            throw new JwtBinderException("The token type in the token header part is not JWT.");

        return new Principal($jwtPayload["sub"], $jwtPayload["roles"], $jwtPayload);
        //TODO: Verify phase and settings
    }
}