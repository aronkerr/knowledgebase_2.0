Ext.define('KnowledgeBase.controller.DocumentList', {
	extend: 'Ext.app.Controller',
	refs: [{
		ref: 'documentlist',
		selector: 'documentlist'
	}],
	stores: ['Documents'],
	models: ['Document'],
	init: function() {
		this.control({
			'documentlist':{
				itemclick: this.onItemClick	
			}
		});
	},
	
	onItemClick: function(tree, record) {
		if(record.data.leaf) {
			this.application.fireEvent('documentopen', record);
		}
	}
});	