<?php

namespace App\Libraries;


use Illuminate\Support\Facades\Auth;

class ACL
{
    /*
    |--------------------------------------------------------------------------
    | Short code meaning
    |--------------------------------------------------------------------------
    |
    | A = Add
    | V = View
    | E = Edit
    | R = Read
    | UP = Update
    |
    */

    public static function getAccsessRight($module, $right = '')
    {
        $accessRight = '';
        if (Auth::user()) {
            $user_role = Auth::user()->role_id;
        } else {
            die('You are not authorized user or your session has been expired!');
        }

        switch ($module) {
            case 'Dashboard':
                if (in_array($user_role, [1, 2])) {
                    $accessRight = 'V-R';
                }
                break;

            case 'Department':
                if (in_array($user_role, [1])) {
                    $accessRight = '-A-V-E-R-UP';
                }
                break;

            case 'User':
                if (in_array($user_role, [1])) {
                    $accessRight = '-A-V-E-R-UP';
                } elseif (in_array($user_role, [2])) {
                    $accessRight = 'V-E-R-UP';
                }
                break;

            case 'Asset':
                if (in_array($user_role, [1])) {
                    $accessRight = '-A-V-E-R-UP';
                }
                break;

            case 'Asset-Category':
                if (in_array($user_role, [1])) {
                    $accessRight = '-A-V-E-R-UP';
                }
                break;

            case 'Allocate':
                if (in_array($user_role, [1])) {
                    $accessRight = '-A-V-E-R-UP';
                }
                break;

            default:
                $accessRight = '';
        }

        if ($right != '') {
            if (strpos($accessRight, $right) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
