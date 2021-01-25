<?php
namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;

class TaskVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::supports()
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        
        if (!$subject instanceof Task) {
            return false;
        }
        
        return true;
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Security\Core\Authorization\Voter\Voter::voteOnAttribute()
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }
        
        /** @var Task $task */
        $task = $subject;
        
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
            case self::DELETE:
                return $this->canDelete($task, $user);
        }
        
        throw new \LogicException('This code should not be reached!');
    }
    
    private function canEdit(Task $task, User $user) {
        return $user === $task->getOwner();
    }
    
    private function canDelete(Task $task, User $user) {
        return $user === $task->getOwner();
    }
}