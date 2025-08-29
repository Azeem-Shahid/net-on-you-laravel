<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\SecurityPolicy;
use App\Models\Setting;

class SecurityModuleUnitTest extends TestCase
{
    /** @test */
    public function admin_permission_system_works()
    {
        // Test permission system without database
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->hasPermission('settings.manage'));
        $this->assertTrue($admin->hasPermission('security.manage'));
        $this->assertTrue($admin->hasPermission('roles.manage'));
    }

    /** @test */
    public function editor_has_limited_permissions()
    {
        $admin = new Admin();
        $admin->role = 'editor';
        
        $this->assertFalse($admin->hasPermission('settings.manage'));
        $this->assertFalse($admin->hasPermission('security.manage'));
        $this->assertTrue($admin->hasPermission('users.manage'));
        $this->assertTrue($admin->hasPermission('magazines.manage'));
    }

    /** @test */
    public function accountant_has_financial_permissions()
    {
        $admin = new Admin();
        $admin->role = 'accountant';
        
        $this->assertFalse($admin->hasPermission('settings.manage'));
        $this->assertFalse($admin->hasPermission('security.manage'));
        $this->assertTrue($admin->hasPermission('transactions.manage'));
        $this->assertTrue($admin->hasPermission('commissions.manage'));
        $this->assertTrue($admin->hasPermission('payouts.manage'));
    }

    /** @test */
    public function admin_role_validation_works()
    {
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->isSuperAdmin());
        $this->assertFalse($admin->isEditor());
        $this->assertFalse($admin->isAccountant());
    }

    /** @test */
    public function editor_role_validation_works()
    {
        $admin = new Admin();
        $admin->role = 'editor';
        
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertTrue($admin->isEditor());
        $this->assertFalse($admin->isAccountant());
    }

    /** @test */
    public function accountant_role_validation_works()
    {
        $admin = new Admin();
        $admin->role = 'accountant';
        
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertFalse($admin->isEditor());
        $this->assertTrue($admin->isAccountant());
    }

    /** @test */
    public function admin_can_manage_users()
    {
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->canManageUsers());
    }

    /** @test */
    public function editor_can_manage_users()
    {
        $admin = new Admin();
        $admin->role = 'editor';
        
        $this->assertTrue($admin->canManageUsers());
    }

    /** @test */
    public function accountant_cannot_manage_users()
    {
        $admin = new Admin();
        $admin->role = 'accountant';
        
        $this->assertFalse($admin->canManageUsers());
    }

    /** @test */
    public function admin_can_manage_finances()
    {
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->canManageFinances());
    }

    /** @test */
    public function accountant_can_manage_finances()
    {
        $admin = new Admin();
        $admin->role = 'accountant';
        
        $this->assertTrue($admin->canManageFinances());
    }

    /** @test */
    public function editor_cannot_manage_finances()
    {
        $admin = new Admin();
        $admin->role = 'editor';
        
        $this->assertFalse($admin->canManageFinances());
    }

    /** @test */
    public function admin_can_manage_magazines()
    {
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->canManageMagazines());
    }

    /** @test */
    public function editor_can_manage_magazines()
    {
        $admin = new Admin();
        $admin->role = 'editor';
        
        $this->assertTrue($admin->canManageMagazines());
    }

    /** @test */
    public function accountant_cannot_manage_magazines()
    {
        $admin = new Admin();
        $admin->role = 'accountant';
        
        $this->assertFalse($admin->canManageMagazines());
    }

    /** @test */
    public function permission_checking_methods_work()
    {
        $admin = new Admin();
        $admin->role = 'super_admin';
        
        $this->assertTrue($admin->hasAnyPermission(['settings.manage', 'users.manage']));
        $this->assertTrue($admin->hasAllPermissions(['settings.manage', 'users.manage']));
        // Super admin has all permissions, even invalid ones
        $this->assertTrue($admin->hasAnyPermission(['invalid.permission']));
        $this->assertTrue($admin->hasAllPermissions(['settings.manage', 'invalid.permission']));
        
        // Test with editor role
        $editor = new Admin();
        $editor->role = 'editor';
        
        $this->assertTrue($editor->hasAnyPermission(['users.manage', 'magazines.manage']));
        $this->assertFalse($editor->hasAnyPermission(['invalid.permission']));
        $this->assertFalse($editor->hasAllPermissions(['users.manage', 'invalid.permission']));
    }
}
