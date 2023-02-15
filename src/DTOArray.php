<?php
namespace Phpnova\DTO;

use Attribute;

#[Attribute()]
class DTOArray
{
    public function __construct(public string $class_name)
    {
        
    }

    public function parceItem($data): object {
        $c = $this->class_name;
        return new $c($data);
    }
}