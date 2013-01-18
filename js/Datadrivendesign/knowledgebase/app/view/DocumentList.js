Ext.define('KnowledgeBase.view.DocumentList', {
	extend: 'Ext.tree.Panel',
	alias: 'widget.documentlist',
	title: 'Document List',
	bodyPadding: 5,
	collapsible: true,
	collapseDirection: 'top',
	store: 'Documents',
	rootVisible: false,
	flex: 1,
	fields: ['id', 'name', 'published', 'location'],
	columns: [{
		xtype: 'treecolumn',
		text: 'Name',
		dataIndex: 'name',
		width: 180,
		sortable: true
	}, {
		text: 'Published',
		dataIndex: 'published',
		flex: 1,
		sortable: true
	}]
});