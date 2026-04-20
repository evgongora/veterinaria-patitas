<?php
/**
 * Modelo base — punto de partida opcional para entidades.
 * La persistencia vive en clases concretas (User, Cliente, Cita, …) usadas por los controladores.
 */

declare(strict_types=1);

class BaseModel
{
    /** @var array<string, mixed> */
    protected array $attributes = [];

    
    public function __construct(array $data = [])
    {
        $this->attributes = $data;
    }

    
    public function toArray(): array
    {
        return $this->attributes;
    }
}
