Ext.define('KnowledgeBase.store.ShoppingCart', {
	extend: 'Ext.data.Store',
	requires: 'KnowledgeBase.model.Item',
	model: 'KnowledgeBase.model.Item',
});