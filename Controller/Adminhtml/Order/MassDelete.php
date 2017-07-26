<?php

namespace TCMP\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class MassDelete
 *
 * @package TCMP\DeleteOrders\Controller\Adminhtml\Order
 */
class MassDelete extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction {
	/**
	 * @param Context           $context
	 * @param Filter            $filter
	 * @param CollectionFactory $collectionFactory
	 */
	public function __construct( Context $context, Filter $filter, CollectionFactory $collectionFactory )
	{
		parent::__construct( $context, $filter );
		$this->collectionFactory = $collectionFactory;
	}

	/**
	 * Delete selected orders
	 *
	 * @param AbstractCollection $collection
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	protected function massAction( AbstractCollection $collection )
	{
		$countDeletedOrder = 0;
		foreach ( $collection->getItems() as $order ) {
			/** @var \Magento\Sales\Model\Order $order */
			$_shipments = $order->getShipmentsCollection();
			if ( $_shipments->getTotalCount() > 0 ) {
				foreach ( $_shipments as $shipment ) {
					$shipment->delete();
				}
			}
			$_invoices = $order->getInvoiceCollection();
			if ( $_invoices->getTotalCount() > 0 ) {
				foreach ( $_invoices as $invoice ) {
					$invoice->delete();
				}
			}
			$_creditMemos = $order->getCreditmemosCollection();
			if ( $_creditMemos->getTotalCount() > 0 ) {
				foreach ( $_creditMemos as $creditMemo ) {
					$creditMemo->delete();
				}
			}
			$order->delete();
			$countDeletedOrder ++;
		}
		$countNonDeletedOrder = $collection->count() - $countDeletedOrder;

		if ( $countNonDeletedOrder && $countDeletedOrder ) {
			$this->messageManager->addError( __( '%1 order(s) cannot be deleted.', $countNonDeletedOrder ) );
		} elseif ( $countNonDeletedOrder ) {
			$this->messageManager->addError( __( 'You cannot delete the order(s).' ) );
		}

		if ( $countDeletedOrder ) {
			$this->messageManager->addSuccess( __( 'We deleted %1 order(s).', $countDeletedOrder ) );
		}
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath( $this->getComponentRefererUrl() );

		return $resultRedirect;
	}

	/**
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed( 'TCMP_DeleteOrders::massDelete' );
	}
}
