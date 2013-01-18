Ext.application({
	requires: [
		'KnowledgeBase.view.Viewport'
	],
	name: 'KnowledgeBase',
	appFolder: '../js/Datadrivendesign/knowledgebase/app',
	controllers: ['DocumentList', 'ItemLookup', 'DocumentView', 'ItemInfo', 'ShoppingCart'],
	launch: function() {
		Ext.create('KnowledgeBase.view.Viewport');
		
		var loadMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
		
		Ext.Ajax.on("beforerequest", function(){
        	console.info("beforerequest");
			loadMask.show();
    	});
		
		Ext.Ajax.on("requestcomplete", function(){
        	console.info("requestcomplete");
			loadMask.hide();
    	});
		
		var queryString = window.location.search;
		if(queryString != ''){
			var document = Ext.Object.fromQueryString(queryString);	
			if(document.id) {			
				Ext.Ajax.request({
					url: 'knowledgebase/index/getDocument',
					params: {
						id: document.id
					},
					success: function(response) {
						var data = JSON.parse(response.responseText);
						if(data.status == 'OK') {
							this.fireEvent('documentopen', data);
						} else {
							// TODO: handle item not being found
							console.log('Item could not be found');
						}
					},
					scope: this
				});
			}
		}
	}	
});