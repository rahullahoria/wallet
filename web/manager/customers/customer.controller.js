(function () {
    'use strict';

    angular
        .module('app')
        .controller('CustomerController', CustomerController);

    CustomerController.$inject = ['UserService', '$cookieStore', 'CandidateService', '$routeParams', 'FlashService','$location'];
    function CustomerController(UserService, $cookieStore, CandidateService,  $routeParams, FlashService,$location) {
        var vm = this;

        vm.user = null;
        vm.inUser = null;
        vm.allUsers = [];
        vm.deleteUser = deleteUser;
        vm.loadUser = loadUser;

        vm.champs = 0;
        vm.good = 0;
        vm.improve = 0;
        vm.bad = 0;

        vm.successFilter = true;
        vm.dangerFilter = true;
        vm.warningFilter = true;
        vm.primaryFilter = true;

        vm.threeMonths = [];
        vm.whichMonth = {};
        vm.loadUser = loadUser;
        vm.currentMonthIndex = 0;
        vm.dataLoading = false;
        vm.subjectTotalQ = 0;

        vm.currentShow = 0;
        vm.store = $routeParams.id

        initController();

        function initController() {
            //  loadCurrentUser();
            // loadAllUsers();

            //loadMonths();
            loadUser();
            loadToCallCandidates();

        }

        vm.setCurrentMon = function(){
            //console.log("i am in setCurrentMonth",vm.currentMonthIndex);

            vm.whichMonth.name = vm.threeMonths[vm.currentMonthIndex].name;
            vm.whichMonth.num = vm.threeMonths[vm.currentMonthIndex].num;
            console.log("i am in setCurrentMonth",vm.whichMonth);
            loadToCallCandidates();

        }



        vm.logout = function(){
            vm.inUser = null;
            UserService.DeleteInUser();
            $location.path('#/login');
        };

        function loadUser(){
            vm.inUser = UserService.GetInUser();
            if(!vm.inUser.name)
                $location.path('/login');
            console.log("in user",vm.inUser);


        }





        vm.loadToCallCandidates = loadToCallCandidates;

        vm.date1 = new Date().getDate();
        vm.getFun = function(work){
            return Math.floor((Math.random() * (work/60/60)) + (work/60/60/4));
        };



        function loadToCallCandidates(){
            vm.dataLoading = true;

            CandidateService.GetCustomers(vm.inUser.org_id)
                .then(function (response) {

                    vm.customers = response.org_details.customers;
                    vm.amounts = response.org_details.amounts;




                    console.log('inside controller',vm.customers);
                });

        }


        function loadAllUsers() {
            UserService.GetAll()
                .then(function (users) {
                    vm.allUsers = users;
                });
        }

        function deleteUser(id) {
            UserService.Delete(id)
                .then(function () {
                    loadAllUsers();
                });
        }





    }

})();