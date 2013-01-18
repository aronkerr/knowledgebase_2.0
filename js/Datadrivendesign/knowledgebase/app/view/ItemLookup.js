Ext.define('KnowledgeBase.view.ItemLookup', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.itemlookup',
	title: 'Item Lookup',
	bodyPadding: 5,
	collapsible: true,
	collapseDirection: 'top',
	height:90,
	fieldDefaults: {
		msgTarget: 'side'
	},
	items: [{
		xtype: 'textfield',
		id: 'searchfield',
		fieldLabel: 'Enter a Part#',
		allowBlank: false		
	}],
	buttons: [{
		text: 'Lookup',
		textAlign: 'center',
		id: 'itemLookupSubmit'
	}]
});