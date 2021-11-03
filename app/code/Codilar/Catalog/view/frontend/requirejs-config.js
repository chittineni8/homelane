var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Codilar_Catalog/js/model/attswitch': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Codilar_Catalog/js/model/swatch-attswitch': true
            }
        }
    }
};