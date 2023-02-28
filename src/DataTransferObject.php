<?php
namespace Phpnova\DTO;

use Exception;
use ReflectionClass;
use ReflectionNamedType;

class DataTransferObject
{
    public function __construct(array|object $data)
    {
        $data = (array)$data;
        $errors = [];
        $reflection_class = new ReflectionClass($this);

        foreach($reflection_class->getProperties() as $property){
            $name = $property->getName();
            $type = $property->getType();
            $attr = $property->getAttributes();

            if (!array_key_exists($name, $data) && !$property->isDefault()){
                $errors[] = $name;
                continue;
            }

            if ($type == "array"){
                $rows = $data[$name];
                if ($attr[0] ?? null && $attr[0]->getName() == "Phpnova\DTO\DTOArray"){
                    /** @var DTOArray */
                    $dto_aray = $attr[0]->newInstance();

                    $rows = array_map(fn($item) => $dto_aray->parceItem($item), $rows);
                }
                $property->setValue($this, $rows);
                continue;
            } 


            if ($type == "object"){
                $property->setValue($this, (object)$data[$name]);
                continue;
            }

            if ($type instanceof ReflectionNamedType){
                if (!$type->isBuiltin()){
                    $class = $class = $type->getName();
                    $property->setValue($this, new $class($data[$name]));
                    continue;
                }
            }
            $property->setValue($this, $data[$name]);
        }

        if (count($errors) > 0){
            $msg = "Faltan los siguientes propiedades en la clase " . $this::class;

            foreach($errors as $err) {
                $msg .= "\n - $err";
            }

            throw new Exception($msg);
        }
    }
}