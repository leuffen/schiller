<?php

namespace App\Config;

class MediaStoreSubscriptionInfo
{
   /**
     * @var string[]
     */
    public array $scopes = [];

    /**
     * @var string[]
     */
    public array $auth = [];

    /**
     * @return string[]
     */
    public function getScopeNames () : array {
        $names = [];
        foreach ($this->scopes as $curScope) {
            $names[] = explode(":", $curScope)[0];
        }
        return $names;
    }


    public function getScopeAccess(string $scope) : ?string {
        foreach ($this->scopes as $curScope) {
            if (strpos($curScope, ":") === -1)
                throw new \InvalidArgumentException("Invalid scope '$curScope'");
            [$curScopeName, $access] = explode(":", $curScope);
            if ($curScopeName === $scope)
                return $access;

        }
        return null;
    }

}
