var ls = ls || {};
ls.plugin = ls.plugin || {};
ls.plugin.page = ls.plugin.page || {};

ls.plugin.page.admin =( function ($) {

	this.ajaxSubmitSimple = function(url,form) {
		form='#'+form;
		ls.ajax.submit(url,form,function(res){
			if (res.sUrlRedirect) {
				window.location.href=res.sUrlRedirect;
			}
			if (res.bReloadPage) {
				window.location.reload();
			}
		}.bind(this),{ validate: false });
	};

	this.createPage = function(form) {
		this.ajaxSubmitSimple(ls.registry.get('sAdminUrl')+'ajax/page-create/',form);
	};

	this.updatePage = function(form) {
		this.ajaxSubmitSimple(ls.registry.get('sAdminUrl')+'ajax/page-update/',form);
	};

	this.removePage = function(id) {
		ls.ajax.load(ls.registry.get('sAdminUrl')+'ajax/page-remove/',{ id: id },function(res){
			if (res.bStateError) {
				ls.msg.error(null, res.sMsg);
			}
			if (res.sUrlRedirect) {
				window.location.href=res.sUrlRedirect;
			}
			if (res.bReloadPage) {
				window.location.reload();
			}
		}.bind(this));
	};

	return this;
}).call(ls.plugin.page.admin || {},jQuery);