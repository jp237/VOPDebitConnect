// This tab will be shown in the customer module

Ext.define('Shopware.apps.VOPDebitConnect.view.detail.CustomerWindow', {
    extend: 'Ext.container.Container',
    padding: 10,
    title: 'BoniGateway',
    initComponent: function() {
        var me = this;
		var record = me.record;
        me.items  =  [{
			layout: 'fit',
            xtype: 'component',
            autoEl: {
                src: 'VOPDebitConnect?fancy=1&switchTo=overViewGateway&pkCustomer='+record.internalId,
                tag: 'iframe',
            	style: 'height: 100%; width: 100%; border: none'
        }
     }];
        me.callParent(arguments);
    }


});