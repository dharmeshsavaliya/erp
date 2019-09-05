<?php

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
            'mailchimp',
            'hubstaff'
	    ];


	    foreach ($permissions as $permission) {
		    Permission::firstOrCreate(['name' => $permission]);
	    }
    }
}
