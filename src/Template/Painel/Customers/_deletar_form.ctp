<?php
    $title = ($this->request->customerId) ? 'Adicionar Cliente' : __('Editar Cliente') ;
    $this->assign('title', $title);
    $this->assign('breadcrumbTitle', $title);
?>

<!-- Breadcrumb -->
<?= $this->element('Painel/breadcrumb', [
    'items' => [
        'Clientes' => $breadcrumb['index'],
    ]
]) ?>

<div ng-app="maiaApp" ng-controller="CustomersFormController as customersForm">
    <div class="main-panels-container">
        <form ng-submit="customersForm.submitForm(form)" name="form" autocomplete="off" novalidate>
            <input type="text" id="id" value="<?= (int)$this->request->customerId ?>">

            <input type="hidden" ng-model="customersForm.customer._csrfToken" value="<?= $this->request->getParam('_csrfToken')?>" id="_csrfToken">

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group" ng-class="{'has-error': (form.name.$dirty && form.name.$invalid) || (form.name.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="name">Nome</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    autocomplete="off"
                                    ng-model="customersForm.customer.name"  
                                    ng-maxlength="200"  
                                    ng-disabled="customersForm.sendingFormData"
                                    required> 
                                <div class="help-block" ng-messages="form.name.$error" ng-if="(form.name.$dirty && form.name.$invalid) || (form.name.$invalid && customersForm.triedSubmitForm)">  
                                    <div ng-message="required">Favor preencher o Nome</div> 
                                    <div ng-message="maxlength">O nome deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                            <div class="form-group" ng-class="{'has-error': (form.email.$dirty && form.email.$invalid) || (form.email.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="email">Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    autocomplete="off"
                                    ng-model="customersForm.customer.email"  
                                    ng-maxlength="200" 
                                    ng-disabled="customersForm.sendingFormData" 
                                    required> 
                                <div class="help-block" ng-messages="form.email.$error" ng-if="(form.email.$dirty && form.email.$invalid) || (form.email.$invalid && customersForm.triedSubmitForm)">  
                                    <div ng-message="email">O Email informado é inválido</div> 
                                    <div ng-message="required">Favor preencher o Nome</div> 
                                    <div ng-message="maxlength">O nome deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-title">
                    Endereço
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" ng-class="{'has-error': (form.zipcode.$dirty && form.zipcode.$invalid) || (form.zipcode.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="zipcode">CEP</label>
                                <input type="text" ng-model="customersForm.customer.address.zipcode" class="form-control" name="zipcode" id="zipcode" ng-maxlength="200" ng-disabled="customersForm.sendingFormData" required>
                                <div class="help-block" ng-messages="form.zipcode.$error" ng-if="(form.zipcode.$dirty && form.zipcode.$invalid) || (form.zipcode.$invalid && customersForm.triedSubmitForm)">
                                    <div ng-message="required">Favor preencher o Bairro</div> 
                                    <div ng-message="maxlength">O Bairro deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="label-control" for="state-id">Estado</label>
                                <select
                                    type="text"
                                    class="form-control" id="state-id"
                                    ng-change="customersForm.statesChange()"
                                    ng-model="customersForm.customer.address.state_id" 
                                    ng-disabled="customersForm.sendingFormData">
                                    <option value="">Selecione:</option>
                                    <option value="{{state.id}}" ng-repeat="state in customersForm.states">{{state.uf}} - {{state.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" ng-class="{'has-error': (form.city_id.$dirty && form.city_id.$invalid) || (form.city_id.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="city_id">Cidade</label>
                                <select
                                    type="text"
                                    id="city_id"
                                    name="city_id"
                                    class="form-control"
                                    ng-disabled="!customersForm.customer.address.state_id || customersForm.loadingCities || customersForm.sendingFormData"
                                    ng-model="customersForm.customer.address.city_id"
                                    required>
                                    <option value="" ng-if="customersForm.cities. length">Selecione:</option>
                                    <option value="" ng-if="!customersForm.customer.address.state_id">Selecione o Estado</option>
                                    <option value="" ng-if="customersForm.loadingCities">Carregando cidades, aguarde...</option>
                                    <option value="{{city.id}}" ng-repeat="city in customersForm.cities">{{city.name}}</option>
                                </select>
                                <div class="help-block" ng-messages="form.city_id.$error" ng-if="(form.city_id.$dirty && form.city_id.$invalid) || (form.city_id.$invalid && customersForm.triedSubmitForm)">   
                                    <div ng-message="required">Favor preencher a Cidade</div>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" ng-class="{'has-error': (form.neighbour.$dirty && form.neighbour.$invalid) || (form.neighbour.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="neighbour">Bairro</label>
                                <input type="text" ng-model="customersForm.customer.address.neighbour" class="form-control" name="neighbour" id="neighbour" ng-maxlength="200" ng-disabled="customersForm.sendingFormData" required>
                                <div class="help-block" ng-messages="form.neighbour.$error" ng-if="(form.neighbour.$dirty && form.neighbour.$invalid) || (form.neighbour.$invalid && customersForm.triedSubmitForm)">
                                    <div ng-message="required">Favor preencher o Bairro</div> 
                                    <div ng-message="maxlength">O Bairro deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                        </div>
 
                        <div class="col-sm-6">
                            <div class="form-group" ng-class="{'has-error': (form.street.$dirty && form.street.$invalid) || (form.street.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="street">Rua</label>
                                <input type="text" ng-model="customersForm.customer.address.street" class="form-control" id="street" name="street" ng-maxlength="200" ng-disabled="customersForm.sendingFormData" required>
                                <div class="help-block" ng-messages="form.street.$error" ng-if="(form.street.$dirty && form.street.$invalid) || (form.street.$invalid && customersForm.triedSubmitForm)">  
                                    <div ng-message="required">Favor preencher a Rua</div> 
                                    <div ng-message="maxlength">A Rua deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-sm-12">
                            <div class="form-group" ng-class="{'has-error': (form.description.$dirty && form.description.$invalid) || (form.description.$invalid && customersForm.triedSubmitForm)}">
                                <label class="label-control" for="description">Endereço</label>
                                <input type="text" ng-model="customersForm.customer.address.description" class="form-control" id="description" name="description" ng-maxlength="200" ng-disabled="customersForm.sendingFormData" required>
                                <div class="help-block" ng-messages="form.description.$error" ng-if="(form.description.$dirty && form.description.$invalid) || (form.description.$invalid && customersForm.triedSubmitForm)">  
 
                                    <div ng-message="required">Favor preencher o Endereço</div> 
                                    <div ng-message="maxlength">O Endereço deve ter no máximo 200 characters</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="panel-title">
                        Telefone<span ng-if="customersForm.customer.phones.length > 1">s</span>
                        <span class="panel-subtitle">
                            {{customersForm.customer.phones.length}} de {{customersForm.maxPhones}}
                        </span>
                    </div>
                    <div ng-repeat="(key, phone) in customersForm.customer.phones">
                        <ng-form name="phones">
                            <div class="row"> 
                                <div class="col-sm-4">
                                    <div class="form-group" >
                                        <label class="label-control" for="phone-company-id-{{key}}">Operadora</label>
                                        <select ng-model="phone.company_id" id="phone-company-id-{{key}}" class="form-control" ng-disabled="customersForm.sendingFormData">
                                            <option value="">Selecione</option>
                                            <option value="{{company.id}}" ng-repeat="company in customersForm.phonesCompanies">{{company.name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" ng-class="{'has-error': (phones.ddd.$invalid && phones.ddd.$dirty) || (phones.ddd.$invalid && customersForm.triedSubmitForm)}">
                                        <label class="label-control" for="phone-dd-{{key}}">DDD</label>
                                        <input type="text" ng-model="phone.ddd" id="phone-dd-{{key}}" name="ddd" class="form-control" ng-disabled="customersForm.sendingFormData" required> 
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div
                                        ng-class="{'has-error': (phones.number.$dirty && phones.number.$invalid) || (customersForm.triedSubmitForm && phones.number.$invalid)}"
                                        class="form-group">
                                        <label class="label-control" for="phone-number-{{key}}">Numero</label>
                                        <input type="text" ng-model="phone.number" id="phone-number-{{key}}" name="number" class="form-control" ng-disabled="customersForm.sendingFormData" required>  
                                    </div>
                                </div>
                                <div class="col-sm-2 text-center" style="margin-top: 31px;">
                                    <button
                                        type="button"
                                        class="btn btn-light btn-xs"
                                        ng-disabled="customersForm.customer.phones.length <= 1 || customersForm.sendingFormData"
                                        ng-click="customersForm.removePhone(key)">
                                        <span class="fa fa-times"></span>
                                    </button>
                                </div>
                            </div>
 
                            <div class="text-danger" ng-messages="phones.ddd.$error" ng-if="(phones.ddd.$invalid && phones.ddd.$dirty) || (phones.ddd.$invalid && customersForm.triedSubmitForm)">   
                                <span class="text-danger" ng-message="required">Favor preencher o DDD</span>  
                            </div>
                            <div ng-messages="phones.number.$error" ng-if="phones.number.$invalid && phones.number.$dirty || customersForm.triedSubmitForm">   
                                <span class="text-danger" ng-message="required">Favor preencher o Numero</span>  
                            </div>
                            <hr>
                        </ng-form>
                    </div> 
                    <div class="text-center" style="margin-top: 20px;">
                        <button type="button" class="btn btn-default" ng-click="customersForm.addPhone(form.$invalid)" ng-disabled="customersForm.customer.phones.length >= customersForm.maxPhones || customersForm.sendingFormData">Adicionar Telefone</button>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="checkbox checkbox-primary ">
                                <input type="checkbox" name="is_active" id="is-active" ng-model="customersForm.customer.is_active" ng-disabled="customersForm.sendingFormData">
                                <label for="is-active">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="panel panel-default">
                <div class="panel-body text-right">
                    <button
                        type="submit"
                        class="btn btn-default"
                        ng-disabled="customersForm.sendingFormData">
                        <span ng-show="customersForm.sendingFormData"><span class="fa fa-spinner fa-spin" ></span></span>
                        <span ng-show="!customersForm.sendingFormData"><span class="fa fa-check" ></span></span> Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>