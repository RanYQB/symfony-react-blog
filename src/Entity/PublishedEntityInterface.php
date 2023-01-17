<?php

namespace App\Entity;

interface PublishedEntityInterface
{
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): PublishedEntityInterface;
}