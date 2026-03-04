<?php

declare(strict_types=1);

namespace Service;

use Core\Session;
use Entity\Utilisateur;
use Repository\UtilisateurRepository;


class AuthService
{
    private UtilisateurRepository $utilisateurRepository;

    public function __construct()
    {
        $this->utilisateurRepository = new UtilisateurRepository();
    }

    public function login(string $email, string $password): ?Utilisateur
    {
        if (empty($email) || empty($password)) {
            return null;
        }

        $utilisateur = $this->utilisateurRepository->findByEmail($email);

        if ($utilisateur === null) {
            return null;
        }

        if (!$utilisateur->getStatut()) {
            throw new \RuntimeException('Ce compte a été désactivé. Contactez l\'administrateur.');
        }

        $verify = password_verify($password, $utilisateur->getMotDePasse());

        if (!$verify) {
            return null;
        }

        $this->storeInSession($utilisateur);
        return $utilisateur;
    }

    private function storeInSession(Utilisateur $utilisateur): void
    {
        Session::regenerate();

        Session::set('user_id',     $utilisateur->getUtilisateurId());
        Session::set('user_nom',    $utilisateur->getNom());
        Session::set('user_prenom', $utilisateur->getPrenom());
        Session::set('user_email',  $utilisateur->getEmail());
        Session::set('user_role',   $this->getRoleLibelle($utilisateur->getRoleId()));

        Session::set('user', [
            'id'     => $utilisateur->getUtilisateurId(),
            'prenom' => $utilisateur->getPrenom(),
            'nom'    => $utilisateur->getNom(),
            'email'  => $utilisateur->getEmail(),
            'role'   => $this->getRoleLibelle($utilisateur->getRoleId()),
        ]);
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function register(array $data): int
    {
        $required = ['nom', 'prenom', 'email', 'mot_de_passe', 'mot_de_passe_confirm', 'telephone', 'adresse', 'code_postal', 'ville'];
        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                throw new \InvalidArgumentException("Le champ {$field} est obligatoire.");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Adresse email invalide.');
        }

        if ($this->utilisateurRepository->emailExists($data['email'])) {
            throw new \RuntimeException('Cette adresse email est déjà utilisée.');
        }

        $this->validatePassword($data['mot_de_passe']);

        if ($data['mot_de_passe'] !== $data['mot_de_passe_confirm']) {
            throw new \InvalidArgumentException('Les mots de passe ne correspondent pas.');
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setRoleId(1);
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setEmail($data['email']);
        $utilisateur->setMotDePasse(password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        $utilisateur->setTelephone($data['telephone']);
        $utilisateur->setAdresse($data['adresse']);
        $utilisateur->setCodePostal($data['code_postal']);
        $utilisateur->setVille($data['ville']);

        return $this->utilisateurRepository->create($utilisateur);
    }

    public function validatePassword(string $password): void
    {
        if (strlen($password) < 10) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins 10 caractères.');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins une majuscule.');
        }
        if (!preg_match('/[a-z]/', $password)) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins une minuscule.');
        }
        if (!preg_match('/[0-9]/', $password)) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins un chiffre.');
        }
        if (!preg_match('/[\W_]/', $password)) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins un caractère spécial.');
        }
    }

    public function generateResetToken(string $email): ?string
    {
        $utilisateur = $this->utilisateurRepository->findByEmail($email);

        if ($utilisateur === null) {
            return null;
        }

        $token    = bin2hex(random_bytes(32));
        $expireAt = new \DateTime('+1 hour');

        $this->utilisateurRepository->saveResetToken(
            $utilisateur->getUtilisateurId(),
            $token,
            $expireAt
        );

        return $token;
    }

    public function resetPassword(string $token, string $newPassword, string $confirm): void
    {
        if ($newPassword !== $confirm) {
            throw new \InvalidArgumentException('Les mots de passe ne correspondent pas.');
        }

        $this->validatePassword($newPassword);

        $utilisateur = $this->utilisateurRepository->findByResetToken($token);

        if ($utilisateur === null) {
            throw new \RuntimeException('Token invalide ou expiré.');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->utilisateurRepository->updatePassword($utilisateur->getUtilisateurId(), $hash);
        $this->utilisateurRepository->invalidateResetToken($token);
    }

    private function getRoleLibelle(int $roleId): string
    {
        return match($roleId) {
            2       => 'employe',
            3       => 'administrateur',
            default => 'utilisateur',
        };
    }
}