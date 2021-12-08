<?php


namespace App\Libraries;

use Illuminate\Support\Facades\Auth;

class CommonFunction
{
    public static function getUserId()
    {
        if (Auth::user()) {
            return Auth::user()->id;
        } else {
            return 'Invalid Login Id';
        }
    }

    /* Show error message */
    public static function showErrorPublic($param, $msg = 'Sorry! Something went wrong! ')
    {
        $j = strpos($param, '(SQL:');
        if ($j > 15) {
            $param = substr($param, 8, $j - 9);
        }
        return $msg . $param;
    }

    /* Get item status */
    public static function getStatus($status)
    {
        if (!empty($status) && $status == 1) {
            $class = 'badge badge-success';
            $status = 'Active';
        } else {
            $class = 'badge badge-danger';
            $status = 'Inactive';
        }
        return '<span class="' . $class . '">' . $status . '</span>';
    }

    public static function dataResponse($success = true, $data = '', $meg = '', $status = 200)
    {
        $data = [
            'success' => $success,
            'data' => $data,
            'message' => $meg,
            'status' => $status
        ];
        return response()->json($data);
    }
}
