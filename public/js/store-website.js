var page = {
    init: function(settings) {
        
        page.config = {
            bodyView: settings.bodyView
        };
        
        $.extend(page.config, settings);
        
        this.getResults();

        //initialize pagination
        page.config.bodyView.on("click",".page-link",function(e) {
        	e.preventDefault();
        	page.getResults($(this).attr("href"));
        });

        page.config.bodyView.on("click",".btn-search-action",function(e) {
            e.preventDefault();
            page.getResults();
        });

        page.config.bodyView.on("click",".btn-add-action",function(e) {
            e.preventDefault();
            page.createRecord();
        });

        // delete product templates
        page.config.bodyView.on("click",".btn-delete-template",function(e) {
            if(!confirm("Are you sure you want to delete record?")) {
                return false;
            }else {
                page.deleteRecord($(this));
            }
        });

        page.config.bodyView.on("click",".btn-edit-template",function(e) {
            page.editRecord($(this));
        });

        $(".common-modal").on("click",".submit-store-site",function() {
            page.submitFormSite($(this));
        });


    },
    validationRule : function() {
         $(document).find("#product-template-from").validate({
            rules: {
                name     : "required",
            },
            messages: {
                name     : "Template name is required",
            }
        })
    },
    loadFirst: function() {
        var _z = {
            url: this.config.baseUrl + "/store-website/records",
            method: "get",
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/records",
            method: "get",
            data : $(".message-search-handler").serialize()
        }
        this.sendAjax(_z, "showResults");
    },
    showResults : function(response) {
    
    	var addProductTpl = $.templates("#template-result-block");
        var tplHtml       = addProductTpl.render(response);

        $(".count-text").html("("+response.total+")");

    	page.config.bodyView.find("#page-view-result").html(tplHtml);

    }
    ,
    deleteRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+ele.data("id")+"/delete",
            method: "get",
        }
        this.sendAjax(_z, 'deleteResults');
    },
    deleteResults : function(response) {
        if(response.code == 200){
            this.getResults();
            toastr['success']('Message deleted successfully', 'success');
        }else{
            toastr['error']('Oops.something went wrong', 'error');
        }

    },
    submitForm: function(ele) {
        var action = $(".store-website-handler").find("#action-to-run").val();
        var ids    = [];
            $.each($(".select-id-input:checked"),function(k,v){
               ids.push($(v).val()); 
            })
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/records/action-handler",
            method: "post",
            data : {"action" : action , "ids" : ids, "_token"  : $('meta[name="csrf-token"]').attr('content')}
        }
        this.sendAjax(_z, "loadFirst");
    },
    createRecord : function(response) {
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render({data:{}});
        
        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");
    },

    editRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+ele.data("id")+"/edit",
            method: "get",
        }
        this.sendAjax(_z, 'editResult');
    },

    editResult : function(response) {
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render(response);
        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");
    },

    submitFormSite : function(ele) {var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/save",
            method: "post",
            data : ele.closest("form").serialize()
        }
        this.sendAjax(_z, "saveSite");
        
    },
    saveSite : function(response) {
        if(response.code  == 200) {
            page.loadFirst();
            $(".common-modal").modal("hide");
        }else {
            toastr["error"](response.error,"");
        }
    }
}

$.extend(page, common);