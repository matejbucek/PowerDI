<?php

namespace PowerDI\Security\OAuth;

use PowerDI\Core\Autowired;
use PowerDI\HttpBasics\HttpRequest;
use PowerDI\Logging\Logger;
use PowerDI\Security\Exceptions\JwtBinderException;
use PowerDI\Security\Principal;
use PowerDI\Security\UserDataBinder;

class JwtUserBinder implements UserDataBinder {
    private string $authServerUrl;

    private string $header;
    #[Autowired("@Logger")]
    private Logger $logger;

    #[Autowired("%app.request%")]
    private HttpRequest $request;

    public function __construct(string $authServerUrl, string $header = "Authorization") {
        $this->authServerUrl = $authServerUrl;
        $this->header = $header;
    }

    public function getUser(): ?Principal {
        $authHeader = $this->request->getHeader($this->header);
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