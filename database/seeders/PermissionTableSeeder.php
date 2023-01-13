<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'selection-list',
            'selection-create',
            'selection-edit',
            'selection-delete',
            'searcher-list',
            'searcher-create',
            'searcher-edit',
            'searcher-delete',
            'setting-list',
            'setting-create',
            'supervisor-list',
            'supervisor-edit',
            'category-edit',
            'imagecropper-list',
            'imagecropper-create',
            'imagecropper-edit',
            'imagecropper-delete',
            'lister-list',
            'lister-edit',
            'approver-list',
            'approver-edit',
            'inventory-list',
            'inventory-edit',
            'attribute-list',
            'attribute-create',
            'attribute-edit',
            'attribute-delete',
            'view-activity',
            'brand-edit',
            'lead-create',
            'lead-edit',
            'lead-delete',
            'crm',
            'order-view',
            'order-create',
            'order-edit',
            'order-delete',
            'admin',
            'reply-edit',
            'purchase',
            'social-create',
            'social-manage',
            'social-view',
            'developer-tasks',
            'developer-all',
            'voucher',
            'review-view',
            'private-viewing',
            'delivery-approval',
            'product-lister',
            'vendor-all',
            'customer',
            'crop-approval',
            'crop-sequence',
            'approved-listing',
            'product-affiliate',
            'social-email',
            'facebook',
            'instagram',
            'sitejabber',
            'pinterest',
            'rejected-listing',
            'instagram-manual-comment',
            'lawyer-all',
            'case-all',
            'seo',
            'old',
            'old-incoming',
            'blogger-all',
            'hubstaff',
            'attachment-create-all',
            'attachment-create-own',
            'attachment-delete-all',
            'attachment-delete-own',
            'attachment-update-all',
            'attachment-update-own',
            'book-create-all',
            'book-create-own',
            'book-delete-all',
            'book-delete-own',
            'book-update-all',
            'book-update-own',
            'book-view-all',
            'book-view-own',
            'bookshelf-create-all',
            'bookshelf-create-own',
            'bookshelf-delete-all',
            'bookshelf-delete-own',
            'bookshelf-update-all',
            'bookshelf-update-own',
            'bookshelf-view-all',
            'bookshelf-view-own',
            'chapter-create-all',
            'chapter-create-own',
            'chapter-delete-all',
            'chapter-delete-own',
            'chapter-update-all',
            'chapter-update-own',
            'chapter-view-all',
            'chapter-view-own',
            'comment-create-all',
            'comment-create-own',
            'comment-delete-all',
            'comment-delete-own',
            'comment-update-all',
            'comment-update-own',
            'image-create-all',
            'image-create-own',
            'image-delete-all',
            'image-delete-own',
            'image-update-all',
            'image-update-own',
            'page-create-all',
            'page-create-own',
            'page-delete-all',
            'page-delete-own',
            'page-update-all',
            'page-update-own',
            'page-view-all',
            'page-view-own',
            'restrictions-manage-all',
            'restrictions-manage-own',
            'settings-manage',
            'templates-manage',
            'user-roles-manage',
            'users-manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}