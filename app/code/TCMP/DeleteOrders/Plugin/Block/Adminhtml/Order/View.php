<?php

namespace TCMP\DeleteOrders\Plugin\Block\Adminhtml\Order;


class View {

	protected $_authorization;

	protected $_urlBuilder;

	protected $_request;

	public function __construct( \Magento\Backend\Block\Template\Context $context, array $data = [] )
	{
		$this->_authorization = $context->getAuthorization();
		$this->_urlBuilder    = $context->getUrlBuilder();
		$this->_request       = $context->getRequest();
	}

	public function beforeSetLayout( \Magento\Sales\Block\Adminhtml\Order\View $view )
	{
		if ( $this->_isAllowedAction( 'TCMP_DeleteOrders::massDelete' ) ) {
			$message = __( 'Are you sure you want to delete the order?' );
			$view->addButton(
				'order_delete',
				[
					'label'   => __( 'Delete' ),
					'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('tcmpsales/order/delete')}')",
				]
			);
		}
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 *
	 * @return bool
	 */
	protected function _isAllowedAction( $resourceId )
	{
		return $this->_authorization->isAllowed( $resourceId );
	}


	/**
	 * Generate url by route and parameters
	 *
	 * @param   string $route
	 * @param   array  $params
	 *
	 * @return  string
	 */
	public function getUrl( $route = '', $params = [] )
	{
		$params['order_id'] = $this->getOrderId();

		return $this->_urlBuilder->getUrl( $route, $params );
	}

	private function getOrderId()
	{
		return $this->_request->getParam( 'order_id' );
	}
}