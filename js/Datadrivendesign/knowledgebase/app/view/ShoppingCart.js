Ext.define('KnowledgeBase.view.ShoppingCart', {
	extend: 'Ext.form.Panel',
	alias: 'widget.shoppingcart',
	title: 'Shopping Cart',
	bodyPadding: 5,
	collapsible: true,
	collapseDirection: 'top',
	items: [{
		xtype: 'gridpanel',
		store: 'ShoppingCart',
		flex: 1,
		columns: [
			{ text: 'productId', dataIndex: 'productId', hidden: true },
			{ text: 'Part#', dataIndex: 'mpn' },
			{ text: 'Name', dataIndex: 'name' },
			{ text: 'Qty', dataIndex: 'qty' }
		]
	}],
	buttons: [{
		text: 'Checkout',
		textAlign: 'center',
		id: 'checkoutSubmit'
	}]
});