<?php


namespace App\Http\Controllers;


use App\Component\ErrorCode;
use App\Models\Category;
use App\Service\Category as CategorySvc;
use Illuminate\Http\Request;

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

    public function add(Request $request, Category $category)
    {
        $uid = $this->getUid();
        if (empty($uid)) {
            return $this->json(ErrorCode::ERROR_NO_LOGIN, 'No Login');
        }
        $icon = $request->input('icon', '');
        $name = $request->input('name', '');
        if (empty($icon) || empty($name)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY);
        }
        $category->uid = $uid;
        $category->cover_url = $icon;
        $category->category_name = $name;
        $result = $category->save();
        if (empty($result)) {
            return $this->json(ErrorCode::ERROR_SQL, 'Add fail');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success', ['id' => $category->id]);
    }

    public function remove(Request $request)
    {
        $uid = $this->getUid();
        $cateId = $request->input('cateId');
        if (empty($cateId)) {
            return $this->json(ErrorCode::ERROR_PARAM_EMPTY, 'Cate empty');
        }
        $cateInfo = Category::find($cateId);
        if (empty($cateInfo)) {
            return $this->json(ErrorCode::DATA_NULL, 'Cate not found');
        }
        if ($cateInfo->uid != $uid) {
            return $this->json(ErrorCode::ERROR_NOT_OWNER, 'Bad Request');
        }
        if ($cateInfo->status != CategorySvc::STATUS_VALID) {
            return $this->json(ErrorCode::ERROR_CATE_INVALID, 'Cate is invalid now');
        }
        $cateInfo->status = CategorySvc::STATUS_DELETED;
        $result = $cateInfo->save();
        if (empty($result)) {
            return $this->json(ErrorCode::ERROR_SQL, 'Operate error');
        }
        return $this->json(ErrorCode::SUCCESS, 'Success');
    }


}
