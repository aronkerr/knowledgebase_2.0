Ext.define('KnowledgeBase.model.Document', {
	extend: 'Ext.data.Model',
	fields: ['id','name','published','doc_id', 'access_key'],
	
	proxy: {
		type: 'ajax',
		url: 'knowledgebase/index/getDocumentTree',
		reader: {
			type: 'json'
		}
	}	
});