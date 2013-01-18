Ext.define('KnowledgeBase.controller.DocumentView', {
	extend: 'Ext.app.Controller',
	refs: [{
		ref: 'documentlist',
		selector: 'documentlist'
	}],
	init: function(){
		this.application.on({
			documentopen: this.onDocumentOpen,
			scope: this
		});
	},
	
	onDocumentOpen: function(record){		
		var doc_id = record.data.doc_id;
		var access_key = record.data.access_key;
		
		var scribd_doc = scribd.Document.getDoc(doc_id, access_key);
		scribd_doc.addParam('jsapi_version', 2);
		scribd_doc.addParam('height', 796);
		scribd_doc.write('document-body');
		
		this.getDocumentlist().collapse();
	}
});