<?php

/**
 * Acquia/CommerceManager/Model/CategoryManagement.php
 *
 * Product Category Extended Data Management API Endpoint
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\CategoryManagementInterface;
use Acquia\CommerceManager\Model\Category\StoreTree;
use Magento\Catalog\Model\CategoryRepository;

/**
 * CategoryManagement
 *
 * Product Category Extended Data Management API Endpoint
 */
class CategoryManagement implements CategoryManagementInterface
{

    /**
     * Magento Category Repository
     * @var CategoryRepository $categoryRepository
     */
    protected $categoryRepository;

    /**
     * Extended Category Tree Instance
     * @var StoreTree $categoryTree
     */
    protected $categoryTree;

    /**
     * Constructor
     *
     * @param CategoryRepository $categoryRepository
     * @param StoreTree $categoryTree
     *
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        StoreTree $categoryTree
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryTree = $categoryTree;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedTree($rootCategoryId = null, $depth = null)
    {
        $category = null;

        if ($rootCategoryId !== null) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryRepository->get($rootCategoryId);
        }

        $result = $this->categoryTree->getTree(
            $this->categoryTree->getRootNode($category),
            $depth
        );

        return ($result);
    }
}
