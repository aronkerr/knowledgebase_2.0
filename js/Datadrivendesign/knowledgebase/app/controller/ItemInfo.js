Ext.define('KnowledgeBase.controller.ItemInfo', {
	extend: 'Ext.app.Controller',
	product: [],
	refs: [{
		ref: 'iteminfo',
		selector: '',
		xtype: 'iteminfo',
		autoCreate: true
	}, {
		ref: 'leftbar',
		selector: '#leftbar'
	}],
	init: function() {	
		this.control({
			'#addToCartSubmit': {
				click: this.onAddToCartSubmit
			}
		});
	
		this.application.on({
			itemrecieved: this.onItemRecieved,
			scope: this
		});
	},
	
	onItemRecieved: function(data) {
		
		var tpl = '\
			<img src="' + data.image + '" />\
			<p>Name: ' + data.name + '</p>\
			<p>Part#: ' + data.mpn + '</p>\
			<p>Price: ' + data.price + '</p>\
		';
		
		this.product = data;
		this.getIteminfo().update(tpl);		
		this.getLeftbar().add(2,this.getIteminfo());
		this.getIteminfo().doLayout();
		// Expand panel if it is closed
		this.getIteminfo().expand()		
	},
	
	onAddToCartSubmit: function(){
		this.application.fireEvent('itemaddtocart', this.product);
	}
});