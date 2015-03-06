<?php
namespace Fminor\Repertoire\Request;

use Fminor\Core\Request\RequestAbstract;

class AssetRequest extends RequestAbstract
{
    const STYLE = 'style';
    const SCRIPT = 'script';
    private $type;
    private $content;
    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        if($type !== self::STYLE && $type !== self::SCRIPT) {
            throw new \InvalidArgumentException(
                'type should be '.self::STYLE.' or '.self::SCRIPT
            );
        }
        $this->type = $type;

        return $this;
    }
}
