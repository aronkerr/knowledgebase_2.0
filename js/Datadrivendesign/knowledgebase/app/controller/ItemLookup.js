Ext.define('KnowledgeBase.controller.ItemLookup', {
	extend: 'Ext.app.Controller',
	refs: [{
		ref: 'itemlookup',
		selector: '',
		xtype: 'itemlookup',
		autoCreate: true
	},{
		ref: 'leftbar',
		selector: '#leftbar',
	}],
	init: function() {
		this.control({
			'#itemLookupSubmit': {
				click: this.onItemLookupSubmit
			}
		});
		
		this.application.on({
			documentopen: this.onDocumentOpen,
			itemlookup: this.onItemLookupSubmit,
			scope: this
		});
	},
	
	onDocumentOpen: function() {
		this.getLeftbar().insert(1,this.getItemlookup());
	},
	
	/* Method to handle item lookup for submission
	 * @param String part number
	 */
	onItemLookupSubmit: function(query) {
		//if(typeof(query) === 'undefined') {
			var query = document.getElementById('searchfield-inputEl').value;
		//}		
		
		// Start new ajax request to get item information
		Ext.Ajax.request({
			url: 'knowledgebase/index/getProductInfo',
			params: {
				q: query
			},
			success: function(response) {
				var data = JSON.parse(response.responseText);
				if(data.status == 'OK') {
					this.application.fireEvent('itemrecieved', data);
				} else {
					Ext.MessageBox.show({
						title: 'Part not found',
						msg: 'Part# ' + query + ' could not be found. Please call 800.247.8103 for help finding what you are looking for.',
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					});
				}
			},
			scope: this
		});
	}
});