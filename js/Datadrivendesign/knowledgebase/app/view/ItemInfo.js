Ext.define('KnowledgeBase.view.ItemInfo', {
	extend: 'Ext.form.Panel',
	alias: 'widget.iteminfo',
	title: 'Item Info',
	bodyPadding: 5,
	collapsible: true,
	collapseDirection: 'top',
	height: 275,
	buttons: [{
		text: 'Add to Cart',
		textAlign: 'center',
		id: 'addToCartSubmit'
	}]
});