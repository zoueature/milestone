<?php


namespace App\Service;

use App\Models\Category as CategoryModel;

class Category extends Service
{
    const STATUS_VALID = 0;
    const STATUS_DELETED = 1;

    const MAX_CATE_NUM = 30;

    private $categoryModel;

    public function __construct(CategoryModel $category)
    {
        $this->categoryModel = $category;
    }

    public function getCategoryInfos(array $cateIds)
    {
        if (empty($cateIds)) {
            return null;
        }
        $cates = $this->categoryModel->getInfos($cateIds);
        return $cates;
    }

    public function allCate(int $uid)
    {
        return $this->categoryModel->getAllByUid($uid);
    }

    public function getCateCount(int $uid) :int
    {
        return $this->categoryModel->getUserCateCount($uid);

    }
}
