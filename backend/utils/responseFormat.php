<?php
//response struct to send

function response($response = 'ok', $message = 'No message', ...$addParam)
{
    $responseBody = [
        'response' => $response,
        'message' => $message
    ];
    if (!empty($addParam)) {
        foreach ($addParam as $key => $value) {
            $responseBody['param' . $key] = $value;
        }
    }
    echo json_encode($responseBody);
}

function object_to_array(Object $object)
{
    $reflector = new ReflectionClass(get_class($object));

    $properties = $reflector->getProperties();
    $array = [];

    foreach ($properties as $property) {
        $getter = 'get' . ucfirst($property->getName());
        $array[$property->getName()] = $object->$getter();
    }

    return $array;
}
