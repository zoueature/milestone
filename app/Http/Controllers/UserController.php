<?php
/*+---------------------------------+
 *| Author: Zoueature               |
 *+---------------------------------+
 *| Email: zoueature@gmail.com      |
 *+---------------------------------+
 *| Date: 2019-10-16 23:01          |
 *+---------------------------------+
 */

namespace App\Http\Controllers;


use App\Component\ErrorCode;
use App\Models\Flag;
use App\Service\Flag as FlagSvc;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userInfo(
        FlagSvc $flagSvc,
        Flag $flag
    ) {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No login');
        }
        $statusNums = $flagSvc->getFlagCount($flag, $uid);
        return $this->json(ErrorCode::SUCCESS, 'Success', $statusNums);
    }

    public function login()
    {
    }
}
