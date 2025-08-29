<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

abstract class BaseDTO
{
    public function __construct(
        public ?int $id = null,
    ) {}

    /**
     * Fields that should not be included in toArray().
     *
     * Override this in child DTOs when needed.
     */
    protected array $hidden = [];

    /**
     * Convert the DTO into an associative array.
     */
    public function toArray(): array
    {
        $data = get_object_vars($this);

        // Remove hidden fields
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }

        // Also remove 'hidden' itself to avoid Mockery issues
        unset($data['hidden']);

        return $data;
    }

    /**
     * Create a new instance of the DTO from an array.
     */
    public static function fromArray(array $data): static
    {
        // Use PHP Reflection to create a new instance of the current class.
        // For each constructor parameter, get the value from $data by parameter name,
        // or use the parameter's default value if not provided in $data.
        // Then instantiate the class with these arguments.
        $reflection = new ReflectionClass(static::class);
        return $reflection->newInstanceArgs(
            array_map(
                function ($param) use ($data) {
                    $name = $param->getName();

                    if (array_key_exists($name, $data)) {
                        return $data[$name];
                    }

                    if ($param->isDefaultValueAvailable()) {
                        return $param->getDefaultValue();
                    }

                    return null; // fallback for non-optional params
                },
                $reflection->getConstructor()->getParameters()
            )
        );
    }

    /**
     * Create a new instance of the DTO using data from a model and an optional array.
     *
     * Values in the provided array take precedence over the model's attributes.
     * If a field is missing in both, the constructor default is used.
     */
    public static function fromModel(Model $model, array $data = []): static
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->newInstanceArgs(
            array_map(
                function ($param) use ($data, $model) {
                    $name = $param->getName();

                    // 1. From data (highest priority)
                    if (array_key_exists($name, $data)) {
                        return $data[$name];
                    }

                    // 2. From model (if attribute exists)
                    if ($model->getAttribute($name) !== null) {
                        return $model->getAttribute($name);
                    }

                    // 3. Fallback to default
                    return $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
                },
                $reflection->getConstructor()->getParameters()
            )
        );
    }
}
