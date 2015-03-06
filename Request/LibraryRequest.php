<?php
namespace Fminor\Repertoire\Request;

use Fminor\Core\Request\RequestAbstract;

class LibraryRequest extends RequestAbstract
{
    private $styleCdns;
    private $stylePaths;
    private $scriptCdns;
    private $scriptPaths;
    public function getStyleCdns()
    {
        return $this->styleCdns;
    }
    public function setStyleCdns(array $styleCdns)
    {
        $this->styleCdns = $styleCdns;

        return $this;
    }
    public function getStylePaths()
    {
        return $this->stylePaths;
    }
    public function setStylePaths(array $stylePaths)
    {
        $this->stylePaths = $stylePaths;

        return $this;
    }
    public function getScriptCdns()
    {
        return $this->scriptCdns;
    }
    public function setScriptCdns(array $scriptCdns)
    {
        $this->scriptCdns = $scriptCdns;

        return $this;
    }
    public function getScriptPaths()
    {
        return $this->scriptPaths;
    }
    public function setScriptPaths(array $scriptPaths)
    {
        $this->scriptPaths = $scriptPaths;

        return $this;
    }
}
