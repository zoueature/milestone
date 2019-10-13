<?php


namespace App\Http\Controllers;


use App\Component\ErrorCode;
use App\Service\Category as CategorySvc;

class CategoryController extends Controller
{
    public function list(CategorySvc $cateSvc)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No Login');
        }
        $allCates = $cateSvc->allCate($uid);
        if (empty($allCates)) {
            return $this->json(ErrorCode::DATA_NULL, 'Empty');
        }
        $result = [];
        foreach ($allCates as $item) {
            $result[] = [
                'id' => $item->id,
                'name' => $item->catrgory_name,
                'img' => $item->cover_url ?: ''
            ];
        }
        return $this->json(ErrorCode::SUCCESS, 'Success', $result);
    }
}
