Ext.define('KnowledgeBase.view.Viewport', {
	extend: 'Ext.panel.Panel',
	requires: [
		'KnowledgeBase.view.DocumentList',
		'KnowledgeBase.view.DocumentView',
		'KnowledgeBase.view.ItemLookup',
		'KnowledgeBase.view.ItemInfo',
		'KnowledgeBase.view.ShoppingCart'
	],
	renderTo: Ext.dom.Query.select('.col-main')[0],
	layout: 'border',
	height: 800,
	items: [{
		region: 'west',
		title: 'Toolbar',
		id: 'leftbar',
		width: 300,
		collapsible: true,
		layout: {
			type: 'vbox',
			align: 'stretch'	
		},
		items: [{
			xtype: 'documentlist'
		}]
	}, {
		region: 'center',
		layout: 'fit',
		items: [{
			xtype: 'documentview',
			flex: 1
		}]
	}]
});