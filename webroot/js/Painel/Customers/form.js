// ^Jquery
// 

$(function() {
  $('[name=zipcode]').mask('99999-999');
  $('[name=ddd]').mask('99');
  $('[name=number]').mask('99999-9999');

  $('#address-state-id').preencheCidades({
      targetSelector: '#address-city-id',
      targetDisabledText: 'Selecione o Estado:',
      key: 'cities',
      label: 'name',
      source: $('#address-state-id').data('url')
  });

});

// angular.module('maiaApp', ['ngMessages'])
//   .controller('CustomersFormController', function($http) {
    
//     var customersForm = this;

//     customersForm.sendingFormData = false;
//     customersForm.triedSubmitForm = false;

//     customersForm.maxPhones = 4;
//     customersForm.states = [];
//     customersForm.cities = [];
//     customersForm.phonesCompanies = [];

//     customersForm.loadingCities = false;

//     customersForm.submitForm = function(form) {

//       customersForm.triedSubmitForm = true;

//       if (form.$invalid) {
//         return;
//       }

//       customersForm.sendingFormData = true;

//       console.log(customersForm.customer);
//       var req = {
//        method: 'POST',
//        url: 'http://localhost/maia/painel/clientes/criar.json',
//        data: customersForm.customer
//       }

//       $http(req).then(function(response) {
//         form.$setPristine(true);
//         customersForm.initForm();
//       })
//       .finally(function() {
//         customersForm.sendingFormData = false;
//       });
//     }

//     customersForm.initForm = function() {

//       customersForm.sendingFormData = false;
//       customersForm.triedSubmitForm = false;

//       customersForm.customer = {
//         _csrfToken: document.getElementById("_csrfToken").value,
//         id: document.getElementById("id").value,
//         name: null,
//         email: null,
//         is_active: true,
//         address: {
//           zipcode: null,
//           neighbour: null,
//           street: null,
//           description: null,
//           state_id: null,
//           city_id: null
//         },
//         phones: [],
//       };

//       if (customersForm.customer.id) {
//         $http.get('http://localhost/maia/painel/clientes/'+customersForm.customer.id+'/editar.json').then(function(response) {
//           console.log('Como form ficou', response.data.customer);
//           customersForm.customer = response.data.customer;
//         });
//       }
//     }

//     customersForm.addPhone = function() {
//       if (customersForm.customer.phones.length >= customersForm.maxPhones) {
//         return;
//       }

//       var phone = {
//         company_id: null,
//         ddd: null,
//         number: null,
//       };

//       customersForm.customer.phones.push(phone);
//     }
    
//     //  Getting data from server
//     $http.get('http://localhost/maia/painel/operadoras.json').then(function(response) {
//       customersForm.phonesCompanies = response.data;
//     });
//     customersForm.loadStates = function() {
//       $http.get('http://localhost/maia/painel/estados.json').then(function(response) {
//         customersForm.states = response.data;
//       });
//     }
//     customersForm.loadCities = function() {
//       customersForm.cities = [];
//       customersForm.loadingCities = true;
//       var req = {
//        method: 'GET',
//        url: 'http://localhost/maia/painel/cidades/por-id-cidade.json',
//        params: {state_id: customersForm.customer.address.state_id}
//       }
//       $http(req).then(function(response) {
//         console.log(response);
//         customersForm.cities = response.data;
//         customersForm.loadingCities = false;
//       });
//     }
//     customersForm.statesChange = function() {
//       if (customersForm.customer.address.state_id) {
//         customersForm.loadCities();
//       } else {
//         customersForm.cities = [];  
//       }
//     }
//     customersForm.removePhone = function(index) {
//       customersForm.customer.phones.splice(index, 1);
//     }


//     customersForm.initForm();
//     customersForm.loadStates();
//     customersForm.addPhone();

//   });