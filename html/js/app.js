alert('holaaaaaaaaaaaaacccccccccccccccccccc');
﻿var ViewModel = function (item) {
    var self = this;
    self.books = ko.observableArray();
    self.labors = ko.observableArray();
    self.error = ko.observable();
    self.detaillabor = ko.observable();
    self.authors = ko.observableArray();
    self.newLabor = {
        Codigo: ko.observable(),
    }

 	var laborsUri = '/proyecto/index/equipoxproyecto/item';

   	function ajaxHelper(uri, method, data) {
        self.error(''); // Clear error message
        return $.ajax({
            type: method,
            url: uri,
            dataType: 'json',
            contentType: 'application/json',
            data: data ? JSON.stringify(data) : null
        }).fail(function (jqXHR, textStatus, errorThrown) {
            self.error(errorThrown);
        });
    }

    	
    function getAllLabors() {
    	//console.log('dsdadada');
        ajaxHelper(laborsUri, 'GET').done(function (data) {
        	console.log(data);
            self.labors(data);
        });
    }

    self.getLaborDetail = function (item) {
        console.log(item);
        // ajaxHelper(laborsUri + item.Id, 'GET').done(function (data) {
        //     console.log(data);
        //     self.detaillabor(data);
        // });
    }


    getAllLabors();
};

//var proyecto=<?php echo $this->proyecto['proyectoid'] ?>;
ko.applyBindings(new ViewModel());