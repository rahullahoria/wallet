(function () {
    'use strict';

    angular
        .module('app')
        .controller('MemberController', MemberController);

    MemberController.$inject = ['UserService', '$cookieStore', 'CandidateService', '$rootScope', 'FlashService','$location'];
    function MemberController(UserService, $cookieStore, CandidateService,  $rootScope, FlashService,$location) {
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

        initController();

        function initController() {
            //  loadCurrentUser();
            // loadAllUsers();

            //loadMonths();
            loadUser();
            loadToCallCandidates();

        }





        vm.logout = function(){
            vm.inUser = null;
            UserService.DeleteInUser();
            $location.path('#/login');
        };

        function loadUser(){
            vm.inUser = UserService.GetInUser();
            if(!vm.inUser.first_name)
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

            CandidateService.GetStatus(vm.inUser.mobile)
                .then(function (response) {
                    vm.orgs = response.orgs;

                    for(var i = 0;i<vm.orgs.length;i++){
                        var balance = 0;
                        var wbalance = 0;
                        for(var j=0;j<vm.orgs[i].trans.length;j++){
                            vm.orgs[i].trans[j].amount = 1*vm.orgs[i].trans[j].amount;
                            if(vm.orgs[i].trans[j].type == 'credit')
                                wbalance +=vm.orgs[i].trans[j].amount;
                            if(vm.orgs[i].trans[j].type == 'debit'){
                                balance -=vm.orgs[i].trans[j].amount;
                                wbalance -=vm.orgs[i].trans[j].amount;
                            }
                            else
                                balance +=vm.orgs[i].trans[j].amount;
                        }

                        vm.orgs[i].balance = balance;
                        vm.orgs[i].wbalance = wbalance;

                    }
                    console.log('inside controller',vm.orgs);
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