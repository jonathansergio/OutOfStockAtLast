<?php
declare(strict_types=1);

namespace Js\OutOfStockAtLast\Plugin\Model\Adapter\BatchDataMapper;

use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;
use Js\OutOfStockAtLast\Model\Elasticsearch\Adapter\DataMapper\Stock as StockDataMapper;
use Js\OutOfStockAtLast\Model\ResourceModel\Inventory;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductDataMapperPlugin
{
    /**
     * @var StockDataMapper
     */
    protected $stockDataMapper;

    /**
     * @var Inventory
     */
    protected $inventory;

    /**
     * ProductDataMapperPlugin constructor.
     * @param StockDataMapper $stockDataMapper
     * @param Inventory $inventory
     */
    public function __construct(StockDataMapper $stockDataMapper, Inventory $inventory)
    {
        $this->stockDataMapper = $stockDataMapper;
        $this->inventory = $inventory;
    }

    /**
     * @param ProductDataMapper $subject
     * @param $documents
     * @param $documentData
     * @param $storeId
     * @param $context
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterMap(
        ProductDataMapper $subject,
        $documents,
        $documentData,
        $storeId,
        $context
    ) {
        $this->inventory->saveRelation(array_keys($documents));

        foreach ($documents as $productId => $document) {
            //@codingStandardsIgnoreLine
            $document = array_merge($document, $this->stockDataMapper->map($productId, $storeId));
            $documents[$productId] = $document;
        }

        $this->inventory->clearRelation();

        return $documents;
    }
}
