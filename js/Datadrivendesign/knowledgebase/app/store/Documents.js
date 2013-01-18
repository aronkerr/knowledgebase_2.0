Ext.define('KnowledgeBase.store.Documents', {
	extend: 'Ext.data.TreeStore',
	requires: 'KnowledgeBase.model.Document',
	model: 'KnowledgeBase.model.Document'
});