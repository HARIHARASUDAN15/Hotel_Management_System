<?php

namespace Razorpay\Api;

use Razorpay\Api\Errors;

class Entity extends Resource implements ArrayableInterface
{
    protected function create($attributes = null)
    {
        return $this->request('POST', $this->getEntityUrl(), $attributes);
    }

    protected function fetch($id)
    {
        $this->validateIdPresence($id);
        return $this->request('GET', $this->getEntityUrl() . $id);
    }

    protected function validateIdPresence($id)
    {
        if ($id !== null) return;

        $class = strtolower((new \ReflectionClass($this))->getShortName());
        throw new Errors\BadRequestError(
            "The {$class} id provided is null",
            Errors\ErrorCode::BAD_REQUEST_ERROR,
            500
        );
    }

    protected function all($options = [])
    {
        return $this->request('GET', $this->getEntityUrl(), $options);
    }

    protected function getEntityUrl()
    {
        return $this->snakeCase(
            (new \ReflectionClass($this))->getShortName()
        ) . 's/';
    }

    protected function snakeCase($input)
    {
        return strtolower(
            preg_replace('/(.)(?=[A-Z])/', '$1_', $input)
        );
    }

    protected function request($method, $relativeUrl, $data = null, $apiVersion = "v1")
    {
        $request = new Request();
        $response = $request->request($method, $relativeUrl, $data, $apiVersion);

        if (isset($response['entity']) && $response['entity'] === $this->getEntity()) {
            $this->fill($response);
            return $this;
        }

        return static::buildEntity($response);
    }

    protected static function buildEntity($data)
    {
        if (!is_array($data)) return new static;

        $entity = new static;

        if (isset($data['entity'])) {
            $class = static::getEntityClass($data['entity']);
            if (class_exists($class)) {
                $entity = new $class;
            }
        }

        $entity->fill($data);
        return $entity;
    }

    protected static function getEntityClass($name)
    {
        return __NAMESPACE__ . '\\' . ucfirst($name);
    }

    protected function getEntity()
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    public function fill($data)
    {
        if (!is_array($data)) return;

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::isAssocArray($value)
                    ? static::buildEntity($value)
                    : array_map(fn($v) => is_array($v) ? static::buildEntity($v) : $v, $value);
            }
            $this->attributes[$key] = $value;
        }
    }

    public static function isAssocArray($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function toArray(): array
    {
        return $this->convertToArray($this->attributes);
    }

    protected function convertToArray($attributes)
    {
        foreach ($attributes as $k => $v) {
            if (is_object($v)) $attributes[$k] = $v->toArray();
            else if (is_array($v)) $attributes[$k] = $this->convertToArray($v);
        }
        return $attributes;
    }

    public function setFile($attributes)
    {
        if (isset($attributes['file'])) {
            $attributes['file'] = new \CURLFile(
                $attributes['file'],
                mime_content_type($attributes['file'])
            );
        }
        return $attributes;
    }
}
