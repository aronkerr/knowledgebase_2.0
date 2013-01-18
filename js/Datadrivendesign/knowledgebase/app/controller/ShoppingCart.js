Ext.define('KnowledgeBase.controller.ShoppingCart', {
	extend: 'Ext.app.Controller',
	refs: [{
		ref: 'shoppingcart',
		selector: '',
		xtype: 'shoppingcart',
		autoCreate: true
	},{
		ref: 'leftbar',
		selector: '#leftbar'
	}],
	stores: ['ShoppingCart'],
	models: ['Item'],
	init: function() {
		this.control({
			'#checkoutSubmit': {				
				click: this.onCheckoutSubmit
			},
			'grid': {
				itemclick: this.onShoppingCartItemCLick
			}
		});
		
		this.application.on({
			itemaddtocart: this.onItemAddToCart,
			scope: this
		});	
	},
	
	onItemAddToCart: function(data) {
		var item = data;
		var store = this.getStore('ShoppingCart');
		var recordIndex = store.find('productId',item.productId);
		this.getLeftbar().insert(3,this.getShoppingcart());
		
		// Force panel to expand if it is closed
		this.getShoppingcart().expand();
		/* Check if item is already in the cart. 
		 * If it's not, add it. If it is, increase the qty by 1
		 */
		if(recordIndex == -1){
			item.qty = 1;
			store.add(item);
		} else {
			var product = store.getAt(recordIndex);
			var qty = product.get('qty');
			qty++;			
			product.set('qty', qty);
			store.commitChanges();
		}	
				
		// Add item to Magento cart
		Ext.Ajax.request({
			url: 'knowledgebase/index/addToCart',
			params: {
				id: item.productId,
				qty: item.qty
			},
			scope: this
		});			
	},
	
	onCheckoutSubmit: function() {
		location.href = './checkout/cart/';		
	},
	
	onShoppingCartItemCLick: function(view, record, item, index, e, eOpts) {
		var query = record.data.productId;
		this.application.fireEvent('itemlookup', query);
	},
	scope: this
});