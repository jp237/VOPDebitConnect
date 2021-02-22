//{block name="backend/order/view/detail/window"}
//{namespace name=backend/order/view/main}
// {$smarty.block.parent}
Ext.define('Shopware.apps.VOPDebitConnect.view.detail.Window', {
	
   override: 'Shopware.apps.Order.view.detail.Window',
	createTabPanel: function () {
		var me = this;
		var tabPanel = me.callParent(arguments);
		tabPanel.add(Ext.create('Shopware.apps.VOPDebitConnect.view.detail.OrderWindow',{ record: me.record } ))
		return tabPanel;
	}

});
//{/block}










