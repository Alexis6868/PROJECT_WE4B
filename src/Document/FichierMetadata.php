<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'fichier_metadata')]
class FichierMetadata
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $filename = '';

    #[ODM\Field(type: 'string')]
    private string $originalName = '';

    #[ODM\Field(type: 'string')]
    private string $mimeType = '';

    #[ODM\Field(type: 'int')]
    private int $size = 0;

    #[ODM\Field(type: 'date_immutable')]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ODM\Field(type: 'int', nullable: true)]
    private ?int $userId = null;

    public function getId(): ?string { return $this->id; }

    public function getFilename(): string { return $this->filename; }
    public function setFilename(string $v): static { $this->filename = $v; return $this; }

    public function getOriginalName(): string { return $this->originalName; }
    public function setOriginalName(string $v): static { $this->originalName = $v; return $this; }

    public function getMimeType(): string { return $this->mimeType; }
    public function setMimeType(string $v): static { $this->mimeType = $v; return $this; }

    public function getSize(): int { return $this->size; }
    public function setSize(int $v): static { $this->size = $v; return $this; }

    public function getUploadedAt(): ?\DateTimeImmutable { return $this->uploadedAt; }
    public function setUploadedAt(\DateTimeImmutable $v): static { $this->uploadedAt = $v; return $this; }

    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $v): static { $this->userId = $v; return $this; }
}
