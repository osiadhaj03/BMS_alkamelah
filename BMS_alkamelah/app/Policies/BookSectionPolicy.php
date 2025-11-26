<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BookSection;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookSectionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookSection');
    }

    public function view(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('View:BookSection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookSection');
    }

    public function update(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('Update:BookSection');
    }

    public function delete(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('Delete:BookSection');
    }

    public function restore(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('Restore:BookSection');
    }

    public function forceDelete(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('ForceDelete:BookSection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookSection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookSection');
    }

    public function replicate(AuthUser $authUser, BookSection $bookSection): bool
    {
        return $authUser->can('Replicate:BookSection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookSection');
    }

}