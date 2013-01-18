Ext.define('KnowledgeBase.model.Item', {
	extend: 'Ext.data.Model',
	fields: [
		{name:'productId', 	type:'int'},
		{name:'mpn',		type:'string'},
		{name:'name',		type:'string'},
		{name:'qty',		type:'int'},
		{name:'image',		type:'string'}
	]
});