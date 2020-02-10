<?php 
namespace Pl\Yd\Observer; 
use Magento\Framework\Event\ObserverInterface; 


class Success implements ObserverInterface { 
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;
    protected $_orderRepositoryInterface;

    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Psr\Log\LoggerInterface $logger, 
        array $data = []

    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->_objectManager = $objectManager;
        $this->_orderRepositoryInterface = $orderRepositoryInterface;
        $this->_logger = $logger;
    }
    public function execute(\Magento\Framework\Event\Observer $observer) { 
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        $orderIds = $observer->getEvent()->getOrderIds();
        $order = $this->_orderRepositoryInterface->get($orderIds[0]);
        $items = $order->getAllItems();
        $shipmetmethod = $order->getShippingMethod();
        //$items = $order->getAllVisibleItems();
        if($shipmetmethod == 'freeshipping_freeshipping'){
            foreach($items as $item) {
                $sku = $item->getSku();
                $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                $oldqty  = $stockItem->getQty();
                $orderqty = $item->getQtyOrdered();
                $qty = $oldqty + $orderqty;
                $stockItem->setQty($qty);
                $stockItem->setIsInStock((bool)$qty); // this line
                $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
                $logger->info('Catched event succssfully <br>sku:-'.$item->getSku().'<br>s_name:-'.$shipmetmethod .'<br>qty:-'.$item->getQtyOrdered().'<br>oldqty:-'. $oldqty.'<br>set qty'.$qty);
            }  
        }else{
           $logger->info('Catched event succssfully shipmetmethod:-'. $shipmetmethod); 
        }  
          
    }
}

