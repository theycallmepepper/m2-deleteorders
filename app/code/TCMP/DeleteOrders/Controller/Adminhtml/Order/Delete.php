<?php

namespace TCMP\DeleteOrders\Controller\Adminhtml\Order;

/**
 * Class Delete
 *
 * @package TCMP\DeleteOrders\Controller\Adminhtml\Order
 */
class Delete extends \Magento\Sales\Controller\Adminhtml\Order {
	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'TCMP_DeleteOrders::delete';

	/**
	 * Cancel order
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		/** @var \Magento\Sales\Model\Order $order */
		$order = $this->_initOrder();
		if ( ! $order ) {
			$this->messageManager->addError( __( 'You have not deleted the item.' ) );

			return $resultRedirect->setPath( 'sales/order/index' );
		}

		if ( $order ) {
			try {
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
				$this->messageManager->addSuccess( __( 'You deleted the order.' ) );
			} catch ( \Magento\Framework\Exception\LocalizedException $e ) {
				$this->messageManager->addError( $e->getMessage() );
			} catch ( \Exception $e ) {
				$this->messageManager->addError( __( 'You have not deleted the order.' ) );
				$this->_objectManager->get( 'Psr\Log\LoggerInterface' )->critical( $e );
			}
		}

		return $resultRedirect->setPath( 'sales/order/index' );
	}

	/**
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed( 'TCMP_DeleteOrders::massDelete' );
	}
}
