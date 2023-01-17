<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['get']]
        ),
        new Put(
            security: "is_granted('ROLE_EDITOR') or (is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user)"
        ),
        new Post(
            denormalizationContext: ['groups' => ['post']],
            security: "is_granted('ROLE_COMMENTATOR')"
        ),
        new GetCollection()]
)]
#[ApiResource(
    uriTemplate: '/blog_posts/{id}/comments',
    operations: [ new GetCollection(
        normalizationContext: ['groups' => ['get-comments']]
    ) ],
    uriVariables: [
        'id' => new Link(toProperty: 'post', fromClass: BlogPost::class),
    ])]
class Comment implements AuthoredEntityinterface, PublishedEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: 'Your post must be at least {{ limit }} characters long',
        maxMessage: 'Your post cannot be longer than {{ limit }} characters',
    )]
    #[Groups(['post', 'get', 'get-comments'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['get', 'post', 'get-comments'])]
    private ?\DateTimeImmutable $published_at = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get', 'post', 'get-comments'])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post'])]
    private ?BlogPost $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeImmutable $published_at): PublishedEntityInterface
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityinterface
    {
        $this->author = $author;

        return $this;
    }

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }
}
