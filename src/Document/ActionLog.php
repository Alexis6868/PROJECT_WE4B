<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'action_log')]
class ActionLog
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $action = '';

    #[ODM\Field(type: 'int', nullable: true)]
    private ?int $userId = null;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $userEmail = null;

    #[ODM\Field(type: 'hash', nullable: true)]
    private ?array $details = null;

    #[ODM\Field(type: 'date_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?string { return $this->id; }

    public function getAction(): string { return $this->action; }
    public function setAction(string $v): static { $this->action = $v; return $this; }

    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $v): static { $this->userId = $v; return $this; }

    public function getUserEmail(): ?string { return $this->userEmail; }
    public function setUserEmail(?string $v): static { $this->userEmail = $v; return $this; }

    public function getDetails(): ?array { return $this->details; }
    public function setDetails(?array $v): static { $this->details = $v; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $v): static { $this->createdAt = $v; return $this; }
}
