<?php

namespace App\Services;

use App\Alert;
use App\Collection;
use App\Role;
use App\User;
use App\UserPermission;
use App\UserRole;

class UserAlertService
{
    public function createForUsers(array $userIds, string $title, ?string $message = null, ?string $url = null, array $context = []): void
    {
        $recipientIds = array_values(array_unique(array_filter($userIds)));
        if (empty($recipientIds)) {
            return;
        }

        $activeUserIds = User::whereIn('id', $recipientIds)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        if (empty($activeUserIds)) {
            return;
        }

        $rows = [];
        $timestamp = now();

        foreach ($activeUserIds as $userId) {
            $rows[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'context' => empty($context) ? null : json_encode($context),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        Alert::insert($rows);
    }

    public function createForRole(int $roleId, string $title, ?string $message = null, ?string $url = null, array $context = []): void
    {
        $userIds = UserRole::where('role_id', $roleId)->pluck('user_id')->toArray();
        $this->createForUsers($userIds, $title, $message, $url, $context);
    }

    public function createForCollectionUsers(Collection $collection, string $title, ?string $message = null, ?string $url = null, array $context = []): void
    {
        $rootCollection = $this->resolveRootCollection($collection);
        $userIds = UserPermission::where('collection_id', $rootCollection->id)
            ->pluck('user_id')
            ->toArray();

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUserIds = UserRole::where('role_id', $adminRole->id)->pluck('user_id')->toArray();
            $userIds = array_merge($userIds, $adminUserIds);
        }

        $this->createForUsers($userIds, $title, $message, $url, $context);
    }

    private function resolveRootCollection(Collection $collection): Collection
    {
        $root = $collection;
        while (!empty($root->parent_id)) {
            $parent = Collection::find($root->parent_id);
            if (!$parent) {
                break;
            }
            $root = $parent;
        }

        return $root;
    }
}
