<?php

declare(strict_types=1);

namespace Entity;

class Role
{
    private ?int   $roleId;
    private string $libelle;

    public function __construct(
        ?int   $roleId  = null,
        string $libelle = ''
    ) {
        $this->roleId  = $roleId;
        $this->libelle = $libelle;
    }

    public function getRoleId(): ?int    { return $this->roleId; }
    public function getLibelle(): string { return $this->libelle; }

    public function setRoleId(?int $id): void      { $this->roleId = $id; }
    public function setLibelle(string $l): void
    {
        if (empty(trim($l))) {
            throw new \InvalidArgumentException('Le libellé ne peut pas être vide.');
        }
        $this->libelle = trim($l);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            roleId:  isset($data['role_id']) ? (int) $data['role_id'] : null,
            libelle: $data['libelle'] ?? '',
        );
    }
}