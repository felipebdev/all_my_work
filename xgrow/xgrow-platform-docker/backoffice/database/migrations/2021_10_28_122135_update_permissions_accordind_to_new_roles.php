<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Role;

class UpdatePermissionsAccordindToNewRoles extends Migration
{

    private function getRoleIdBySlug($roles, $slug){
        return $roles->where('slug', $slug)->first()->id;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $all = Role::get();

        $roles = [
            $this->getRoleIdBySlug($all, 'product') => [$this->getRoleIdBySlug($all, 'coupons')],
            $this->getRoleIdBySlug($all, 'author') => [$this->getRoleIdBySlug($all, 'transfer-content')],
            $this->getRoleIdBySlug($all, 'subscriber') => [$this->getRoleIdBySlug($all, 'import-suscriber')],
            $this->getRoleIdBySlug($all, 'report') => [$this->getRoleIdBySlug($all, 'content-report'), $this->getRoleIdBySlug($all, 'search-report'), $this->getRoleIdBySlug($all, 'course-report')],
            $this->getRoleIdBySlug($all, 'config') => [$this->getRoleIdBySlug($all, 'user'), $this->getRoleIdBySlug($all, 'permission'), $this->getRoleIdBySlug($all, 'category')],
        ];


        foreach($roles as $id => $role){
            $permissions = DB::table('permission_role')->where('role_id', $id)->get();

            foreach($permissions as $permission){   
                foreach($role as $child){
                    $hasRole = DB::table('permission_role')->where('role_id', $child)->where('permission_id', $permission->permission_id)->get();
                    if($hasRole->count() == 0)
                     DB::table('permission_role')->insert(['role_id' => $child, 'permission_id' => $permission->permission_id]);
                }
            }

        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $all = Role::get();

        $roles = [
            $this->getRoleIdBySlug($all, 'product') => [$this->getRoleIdBySlug($all, 'coupons')],
            $this->getRoleIdBySlug($all, 'author') => [$this->getRoleIdBySlug($all, 'transfer-content')],
            $this->getRoleIdBySlug($all, 'subscriber') => [$this->getRoleIdBySlug($all, 'import-suscriber')],
            $this->getRoleIdBySlug($all, 'report') => [$this->getRoleIdBySlug($all, 'content-report'), $this->getRoleIdBySlug($all, 'search-report'), $this->getRoleIdBySlug($all, 'course-report')],
            $this->getRoleIdBySlug($all, 'config') => [$this->getRoleIdBySlug($all, 'user'), $this->getRoleIdBySlug($all, 'permission'), $this->getRoleIdBySlug($all, 'category')],
        ];


        foreach($roles as $id => $role){
            $permissions = DB::table('permission_role')->where('role_id', $id)->get();
            foreach($permissions as $permission){   
                foreach($role as $child){
                    DB::table('permission_role')->where(['role_id' => $child, 'permission_id' => $permission->permission_id])->delete();
                }
            }

        }
    }
}
