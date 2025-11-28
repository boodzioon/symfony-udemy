<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoVoter extends Voter
{
    public const DELETE = 'VIDEO_DELETE';
    public const EDIT = 'VIDEO_EDIT';
    public const VIEW = 'VIDEO_VIEW';

    protected function supports($attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Video;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $video = $subject;

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DELETE:
                return $user === $video->getSecurityUser();
                break;
            case self::EDIT:
                return $user === $video->getSecurityUser();
                break;
            case self::VIEW:
                return !empty($user);
                break;
        }

        return false;
    }
}
