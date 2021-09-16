var config = {
    map: {
        '*': {
          'Magento_Checkout/template/form/element/email.html': 
              'Magedelight_SMSProfile/template/form/element/email.html',
          'Magento_OfflinePayments/template/payment/cashondelivery.html':
          	'Magedelight_SMSProfile/template/payment/cashondelivery.html',
          'Magento_Checkout/template/authentication.html': 
              'Magedelight_SMSProfile/template/authentication.html',
          'Magento_OfflinePayments/js/view/payment/method-renderer/cashondelivery-method' : 
              'Magedelight_SMSProfile/js/payment/cashondelivery-method',    	
        }        
    }
};