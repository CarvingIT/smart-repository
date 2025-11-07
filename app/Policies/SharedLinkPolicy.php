<?php

namespace App\Policies;

use App\SharedLink;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SharedLinkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Users can view their own shared links
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\SharedLink  $sharedLink
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SharedLink $sharedLink)
    {
        // User can view their own shared links or admins can view all
        return $user->id === $sharedLink->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @param  int  $documentId
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, $documentId = null)
    {
        // If no document ID provided, cannot determine permission
        if (!$documentId) {
            return false;
        }
        
        // Check if user has CAN_SHARE permission for this document
        return $user->canShareDocument($documentId);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\SharedLink  $sharedLink
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SharedLink $sharedLink)
    {
        return $user->id === $sharedLink->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SharedLink  $sharedLink
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SharedLink $sharedLink)
    {
        return $user->id === $sharedLink->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\SharedLink  $sharedLink
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SharedLink $sharedLink)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\SharedLink  $sharedLink
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SharedLink $sharedLink)
    {
        //
    }
}
