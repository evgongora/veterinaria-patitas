<?php
/**
 * Modelo base — punto de partida para modelos de datos (BD, entidades).
 * La lógica de persistencia se integrará en entregas posteriores.
 */

declare(strict_types=1);

class BaseModel
{
    /** @var array<string, mixed> */
    protected array $attributes = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->attributes = $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
