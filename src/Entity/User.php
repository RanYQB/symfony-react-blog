<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get'],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Put(
            denormalizationContext: ['groups' => 'put'],
            security: "is_granted('IS_AUTHENTICATED_FULLY') and object == user"
        ),
        new Post(
            denormalizationContext: ['groups' => 'post']
        ),
        new GetCollection()
    ],
    normalizationContext: ['groups' => 'get']
)]
#[UniqueEntity(fields: ['email', 'username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['get'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get', 'post'])]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post', 'put'])]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'L\'adresse mail {{value}} n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    #[Groups(['get', 'post', 'put'])]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'Votre nom ne peut pas contenir de chiffres.',
        match: false,
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 100)]
    #[Groups(['get', 'post', 'put'])]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/\d/',
        message: 'Votre prénom ne peut pas contenir de chiffres.',
        match: false,
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post', 'put'])]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{7,}/',
        message: 'Votre mot de passe doit contenir au minimum sept caractères, une majuscule, une minuscule et un chiffre.',
        match: true,
    )]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Groups(['post', 'put'])]
    #[Assert\Expression(
        'this.getPassword() === this.getConfirmedPassword()',
        message: 'Les mots de passe doivent correspondre'
    )]
    private ?string $confirmedPassword = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: BlogPost::class)]
    #[Groups(['get'])]
    private Collection $blogPosts;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    #[Groups(['get'])]
    private Collection $comments;



    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmedPassword(): ?string
    {
        return $this->confirmedPassword;
    }

    public function setConfirmedPassword(string $confirmedPassword): self
    {
        $this->confirmedPassword = $confirmedPassword;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, BlogPost>
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts->add($blogPost);
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }
}
