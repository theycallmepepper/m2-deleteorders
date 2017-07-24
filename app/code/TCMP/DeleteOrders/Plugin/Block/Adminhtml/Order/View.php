<?php

namespace TCMP\DeleteOrders\Plugin\Block\Adminhtml\Order;


use Magento\Sales\Block\Adminhtml\Order\View as OGView;


class View extends OGView {

	public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $view )
	{
		if ( $this->_isAllowedAction( 'TCMP_DeleteOrders::massDelete' ) ) {
			$message = __( 'Are you sure you want to delete the order?' );
			$this->buttonList->add(
				'order_delete',
				[
					'label'   => __( 'Delete' ),
					'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('tcmpsales/order/delete')}')",
				]
			);
		}
	}
}