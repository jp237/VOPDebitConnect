//{block name="backend/customer/view/detail/window"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.VOPDebitConnect.view.detail.Window', {
	
  	override: 'Shopware.apps.Customer.view.detail.Window',
	getTabs: function () {
		var me = this;
		var tabPanel = me.callParent(arguments);
		tabPanel.push(Ext.create('Shopware.apps.VOPDebitConnect.view.detail.CustomerWindow',{ record: me.record } ))
		return tabPanel;
	},
	

});
//{/block}










